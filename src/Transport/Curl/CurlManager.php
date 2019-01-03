<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Curl;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Hanaboso\CommonsBundle\Exception\DateTimeException;
use Hanaboso\CommonsBundle\Metrics\InfluxDbSender;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\ResponseDto;
use Hanaboso\CommonsBundle\Transport\CurlManagerInterface;
use Hanaboso\CommonsBundle\Transport\Utils\TransportFormatter;
use Hanaboso\CommonsBundle\Utils\CurlMetricUtils;
use Hanaboso\CommonsBundle\Utils\ExceptionContextLoader;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;

/**
 * Class CurlManager
 *
 * @package Hanaboso\CommonsBundle\Transport\Curl
 */
class CurlManager implements CurlManagerInterface, LoggerAwareInterface
{

    public const METHOD_GET     = 'GET';
    public const METHOD_POST    = 'POST';
    public const METHOD_HEAD    = 'HEAD';
    public const METHOD_PUT     = 'PUT';
    public const METHOD_DELETE  = 'DELETE';
    public const METHOD_OPTIONS = 'OPTIONS';
    public const METHOD_PATCH   = 'PATCH';

    /**
     * @var CurlClientFactory
     */
    private $curlClientFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var InfluxDbSender|null
     */
    private $influxSender;

    /**
     * @var array
     */
    private $startTimes = [];

    /**
     * CurlManager constructor.
     *
     * @param CurlClientFactory $curlClientFactory
     */
    public function __construct(CurlClientFactory $curlClientFactory)
    {
        $this->curlClientFactory = $curlClientFactory;
        $this->logger            = new NullLogger();
        $this->influxSender      = NULL;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return CurlManager
     */
    public function setLogger(LoggerInterface $logger): CurlManager
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param InfluxDbSender $influxSender
     *
     * @return CurlManager
     */
    public function setInfluxSender(InfluxDbSender $influxSender): CurlManager
    {
        $this->influxSender = $influxSender;

        return $this;
    }

    /**
     * @return array
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
     * @param RequestDto $dto
     * @param array      $options
     *
     * @return ResponseDto
     * @throws CurlException
     */
    public function send(RequestDto $dto, array $options = []): ResponseDto
    {
        $request = new Request($dto->getMethod(), $dto->getUri(), $dto->getHeaders(), $dto->getBody());

        try {
            $this->logger->debug(TransportFormatter::requestToString(
                $dto->getMethod(),
                (string) $dto->getUri(),
                $dto->getHeaders(),
                $dto->getBody()
            ));

            $client = $this->curlClientFactory->create();

            $this->startTimes = CurlMetricUtils::getCurrentMetrics();
            $psrResponse      = $client->send($request, $this->prepareOptions($options));
            $this->sendMetrics($dto);

            $response = new ResponseDto(
                $psrResponse->getStatusCode(),
                $psrResponse->getReasonPhrase(),
                $psrResponse->getBody()->getContents(),
                $psrResponse->getHeaders()
            );

            $this->logger->debug(TransportFormatter::responseToString(
                $psrResponse->getStatusCode(),
                $psrResponse->getReasonPhrase(),
                $psrResponse->getHeaders(),
                $psrResponse->getBody()->getContents()
            ));

            unset($psrResponse);
        } catch (RequestException $exception) {
            $this->sendMetrics($dto);
            $response = $exception->getResponse();
            $message  = $exception->getMessage();
            if ($response) {
                $message = $response->getBody()->getContents();
                $response->getBody()->rewind();
            }
            $this->logger->error(
                sprintf('CurlManager::send() failed: %s', $message),
                ExceptionContextLoader::getContextForLogger($exception)
            );

            throw new CurlException(
                sprintf('CurlManager::send() failed: %s', $message),
                CurlException::REQUEST_FAILED,
                $exception->getPrevious(),
                $response
            );
        } catch (Throwable | GuzzleException $exception) {
            $this->sendMetrics($dto);
            $this->logger->error(
                sprintf('CurlManager::send() failed: %s', $exception->getMessage()),
                ExceptionContextLoader::getContextForLogger($exception)
            );
            throw new CurlException(
                sprintf('CurlManager::send() failed: %s', $exception->getMessage()),
                CurlException::REQUEST_FAILED,
                $exception->getPrevious()
            );
        }

        return $response;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    protected function prepareOptions(array $options): array
    {
        return array_merge(['http_errors' => FALSE], $options);
    }

    /**
     * @param RequestDto $dto
     *
     * @throws CurlException
     */
    protected function sendMetrics(RequestDto $dto): void
    {
        if ($this->influxSender !== NULL) {
            $info  = $dto->getDebugInfo();
            $times = CurlMetricUtils::getTimes($this->startTimes);

            try {
                CurlMetricUtils::sendCurlMetrics(
                    $this->influxSender,
                    $times,
                    $info['node_id'][0] ?? NULL,
                    $info['correlation_id'][0] ?? NULL
                );
            } catch (DateTimeException $e) {
                throw new CurlException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }

}