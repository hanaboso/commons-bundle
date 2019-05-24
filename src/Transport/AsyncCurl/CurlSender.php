<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\AsyncCurl;

use Clue\React\Buzz\Browser;
use Clue\React\Buzz\Message\ResponseException;
use Exception;
use GuzzleHttp\Psr7\Request;
use Hanaboso\CommonsBundle\Exception\DateTimeException;
use Hanaboso\CommonsBundle\Metrics\InfluxDbSender;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;
use Hanaboso\CommonsBundle\Transport\Utils\TransportFormatter;
use Hanaboso\CommonsBundle\Utils\CurlMetricUtils;
use Hanaboso\CommonsBundle\Utils\ExceptionContextLoader;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use React\Promise\PromiseInterface;
use function React\Promise\reject;
use function React\Promise\resolve;

/**
 * Class CurlSender
 *
 * @package Hanaboso\CommonsBundle\Transport\AsyncCurl
 */
class CurlSender implements LoggerAwareInterface
{

    /**
     * @var Browser
     */
    private $browser;

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
     * CurlSender constructor.
     *
     * @param Browser $browser
     */
    public function __construct(Browser $browser)
    {
        $this->browser      = $browser;
        $this->logger       = new NullLogger();
        $this->influxSender = NULL;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param InfluxDbSender $influxSender
     *
     * @return CurlSender
     */
    public function setInfluxSender(InfluxDbSender $influxSender): CurlSender
    {
        $this->influxSender = $influxSender;

        return $this;
    }

    /**
     * @param RequestDto $dto
     *
     * @return PromiseInterface
     */
    public function send(RequestDto $dto): PromiseInterface
    {
        $request = new Request($dto->getMethod(), $dto->getUri(), $dto->getHeaders(), $dto->getBody());

        $this->logRequest($request, $dto->getDebugInfo());
        $this->startTimes = CurlMetricUtils::getCurrentMetrics();

        return $this
            ->sendRequest($request)
            ->then(
                function (ResponseInterface $response) use ($dto) {
                    $this->logResponse($response, $dto->getDebugInfo());
                    $this->sendMetrics($dto);

                    return resolve($response);
                },
                function (Exception $e) use ($dto) {
                    $this->sendMetrics($dto);

                    if ($e instanceof ResponseException) {
                        $this->logResponse($e->getResponse(), $dto->getDebugInfo());
                    } else {
                        $this->logger->error(
                            sprintf('Async request error: %s', $e->getMessage()),
                            array_merge(ExceptionContextLoader::getContextForLogger($e), $dto->getDebugInfo())
                        );
                    }

                    return reject($e);
                }
            );
    }

    /**
     * @param RequestInterface $request
     *
     * @return PromiseInterface
     */
    private function sendRequest(RequestInterface $request): PromiseInterface
    {
        return $this->browser->send($request);
    }

    /**
     * @param RequestInterface $request
     * @param array            $debugInfo
     */
    private function logRequest(RequestInterface $request, array $debugInfo = []): void
    {
        $message = TransportFormatter::requestToString(
            $request->getMethod(),
            (string) $request->getUri(),
            $request->getHeaders(),
            $request->getBody()->getContents()
        );

        $this->logger->debug($message, $debugInfo);
    }

    /**
     * @param ResponseInterface $response
     * @param array             $debugInfo
     */
    private function logResponse(ResponseInterface $response, array $debugInfo = []): void
    {
        $message = TransportFormatter::responseToString(
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $response->getHeaders(),
            $response->getBody()->getContents()
        );
        $this->logger->debug($message, $debugInfo);

        $response->getBody()->rewind();
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
