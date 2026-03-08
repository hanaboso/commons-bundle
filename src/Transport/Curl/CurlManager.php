<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Curl;

use Exception;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
use Hanaboso\CommonsBundle\Traits\MetricsTrait;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\ResponseDto;
use Hanaboso\CommonsBundle\Transport\CurlManagerInterface;
use Hanaboso\CommonsBundle\Utils\CurlMetricUtils;
use Hanaboso\Utils\String\LoggerFormater;
use Hanaboso\Utils\Traits\LoggerTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\NullLogger;
use Throwable;

/**
 * Class CurlManager
 *
 * @package Hanaboso\CommonsBundle\Transport\Curl
 */
final class CurlManager implements CurlManagerInterface, LoggerAwareInterface
{

    use LoggerTrait;
    use MetricsTrait;

    public const string METHOD_GET     = 'GET';
    public const string METHOD_POST    = 'POST';
    public const string METHOD_HEAD    = 'HEAD';
    public const string METHOD_PUT     = 'PUT';
    public const string METHOD_DELETE  = 'DELETE';
    public const string METHOD_OPTIONS = 'OPTIONS';
    public const string METHOD_PATCH   = 'PATCH';

    /**
     * @var int
     */
    private int $timeout;

    /**
     * CurlManager constructor.
     *
     * @param CurlClientFactory $curlClientFactory
     */
    public function __construct(private CurlClientFactory $curlClientFactory)
    {
        $this->logger        = new NullLogger();
        $this->metricsSender = NULL;
        $this->timeout       = 30;
    }

    /**
     * @param MetricsSenderLoader $metricsSender
     *
     * @return CurlManager
     */
    public function setMetricsSender(MetricsSenderLoader $metricsSender): self
    {
        $this->metricsSender = $metricsSender;

        return $this;
    }

    /**
     * @param int $timeout
     *
     * @return CurlManager
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @param RequestDto $dto
     * @param mixed[]    $options
     *
     * @return ResponseDto
     * @throws CurlException
     */
    public function send(RequestDto $dto, array $options = []): ResponseDto
    {
        try {
            $this->logBeforeSend($dto);
            $client = $this->curlClientFactory->create(['timeout' => $this->timeout]);

            $this->startTimes = CurlMetricUtils::getCurrentMetrics();
            $psrResponse      = $client->send($this->createRequest($dto), $this->prepareOptions($options));
            $code             = $psrResponse->getStatusCode();
            $body             = $psrResponse->getBody()->getContents();
            $this->sendMetrics($dto, $code, $code >= 300 ? $body : NULL);

            $response = new ResponseDto($code, $psrResponse->getReasonPhrase(), $body, $psrResponse->getHeaders());

            $this->logAfterSend($psrResponse, $dto);
            unset($psrResponse);

            return $response;
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
            $message  = $exception->getMessage();
            $code     = $exception->getCode();
            if ($response) {
                $message = $response->getBody()->getContents();
                $code    = $response->getStatusCode();
                $response->getBody()->rewind();
            }
            $this->sendMetrics($dto, $code, $message);
            $this->logAfterError($exception, $dto, $message);

            throw $this->throwCurlError($exception, $message, $response);
        } catch (Throwable $exception) {
            $this->sendMetrics($dto, 500, 'Unknown error');
            $this->logAfterError($exception, $dto);

            throw  $this->throwCurlError($exception);
        }
    }

    /**
     * @param RequestDto $dto
     * @param mixed[]    $options
     *
     * @return PromiseInterface
     */
    public function sendAsync(RequestDto $dto, array $options = []): PromiseInterface
    {
        $this->logBeforeSend($dto);
        $client           = $this->curlClientFactory->create(['timeout' => $this->timeout]);
        $this->startTimes = CurlMetricUtils::getCurrentMetrics();

        return $client
            ->sendAsync($this->createRequest($dto), $this->prepareOptions($options))
            ->then(
                function (ResponseInterface $response) use ($dto): ResponseInterface {
                    $this->logResponse($response);
                    $code = $response->getStatusCode();
                    $this->sendMetrics($dto, $code, $code >= 300 ? $response->getBody()->getContents() : NULL);
                    $response->getBody()->rewind();

                    return $response;
                },
                function (Exception $e) use ($dto): void {
                    if ($e instanceof RequestException) {
                        $response = $e->getResponse();
                        $message  = $e->getMessage();
                        $code     = $e->getCode();
                        if ($response) {
                            $message = $response->getBody()->getContents();
                            $code    = $response->getStatusCode();
                            $response->getBody()->rewind();
                        }
                        $this->sendMetrics($dto, $code, $message);
                        $this->logAfterError($e, $dto, $message);
                    } else {
                        $this->sendMetrics($dto, 500, 'Unknown error');
                        $this->logAfterError($e, $dto);
                    }

                    throw $e;
                },
            );
    }

    /**
     * @return mixed[]
     */
    public static function getMethods(): array
    {
        return [
            self::METHOD_GET,
            self::METHOD_POST,
            self::METHOD_HEAD,
            self::METHOD_PUT,
            self::METHOD_DELETE,
            self::METHOD_OPTIONS,
            self::METHOD_PATCH,
        ];
    }

    /**
     * @param mixed[] $options
     *
     * @return mixed[]
     */
    protected function prepareOptions(array $options): array
    {
        return array_merge(['http_errors' => FALSE], $options);
    }

    /**
     * @param RequestDto $dto
     */
    protected function logBeforeSend(RequestDto $dto): void
    {
        $this->logger->debug(
            LoggerFormater::requestToString(
                $dto->getMethod(),
                (string) $dto->getUri(),
                $dto->getHeaders(),
                $dto->getBody(),
            ),
            $dto->getDebugInfo(),
        );
    }

    /**
     * @param ResponseInterface $response
     * @param RequestDto        $dto
     */
    protected function logAfterSend(ResponseInterface $response, RequestDto $dto): void
    {
        $this->logResponse($response, $dto->getDebugInfo());
    }

    /**
     * @param Throwable   $t
     * @param RequestDto  $dto
     * @param string|null $message
     */
    protected function logAfterError(Throwable $t, RequestDto $dto, ?string $message = NULL): void
    {
        $this->logger->error(
            sprintf('CurlManager::send() failed: %s', $message ?? $t->getMessage()),
            LoggerFormater::getContextForLogger($t, $dto->getDebugInfo()),
        );
    }

    /**
     * @param Throwable              $t
     * @param string|null            $message
     * @param ResponseInterface|null $response
     *
     * @return CurlException
     */
    protected function throwCurlError(
        Throwable $t,
        ?string $message = NULL,
        ?ResponseInterface $response = NULL,
    ): CurlException
    {
        return new CurlException(
            sprintf('CurlManager::send() failed: %s', $message ?? $t->getMessage()),
            CurlException::REQUEST_FAILED,
            $t->getPrevious(),
            $response,
        );
    }

    /**
     * @param RequestDto $dto
     *
     * @return Request
     */
    private function createRequest(RequestDto $dto): Request
    {
        return new Request($dto->getMethod(), $dto->getUri(), $dto->getHeaders(), $dto->getBody());
    }

    /**
     * @param ResponseInterface $response
     * @param mixed[]           $context
     */
    private function logResponse(ResponseInterface $response, array $context = []): void
    {
        $this->logger->debug(
            LoggerFormater::responseToString(
                $response->getStatusCode(),
                $response->getReasonPhrase(),
                $response->getHeaders(),
                $response->getBody()->getContents(),
            ),
            $context,
        );
        $response->getBody()->rewind();
    }

}
