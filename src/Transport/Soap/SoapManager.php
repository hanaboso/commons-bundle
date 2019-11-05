<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Soap;

use Exception;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\RequestDtoAbstract;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseDto;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseHeaderDto;
use Hanaboso\CommonsBundle\Utils\CurlMetricUtils;
use Hanaboso\CommonsBundle\Utils\ExceptionContextLoader;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class SoapManager
 *
 * @package Hanaboso\CommonsBundle\Transport\Soap
 */
final class SoapManager implements SoapManagerInterface, LoggerAwareInterface
{

    public const CONNECTION_TIMEOUT = 15;

    /**
     * @var SoapClientFactory
     */
    private $soapClientFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MetricsSenderLoader|null
     */
    private $metricsSender;

    /**
     * @var array
     */
    private $startTimes = [];

    /**
     * SoapManager constructor.
     *
     * @param SoapClientFactory $soapClientFactory
     */
    public function __construct(SoapClientFactory $soapClientFactory)
    {
        $this->soapClientFactory = $soapClientFactory;
        $this->logger            = new NullLogger();
        $this->metricsSender     = NULL;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return SoapManager
     */
    public function setLogger(LoggerInterface $logger): SoapManager
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param MetricsSenderLoader $metricsSender
     *
     * @return SoapManager
     */
    public function setMetricsSender(MetricsSenderLoader $metricsSender): SoapManager
    {
        $this->metricsSender = $metricsSender;

        return $this;
    }

    /**
     * @param RequestDtoAbstract $request
     * @param array              $options
     *
     * @return ResponseDto
     * @throws SoapException
     */
    public function send(RequestDtoAbstract $request, array $options = []): ResponseDto
    {
        try {
            $client = $this->soapClientFactory->create($request, $this->composeOptions($request, $options));

            $this->logger->debug(
                sprintf(
                    'Request: Type: %s, Uri: %s, Headers: %s, User: %s, Password: %s',
                    $request->getType(),
                    $request->getUri(),
                    $this->getHeadersAsString($request->getHeader()->getParams()),
                    $request->getUser(),
                    $request->getPassword()
                )
            );

            $this->startTimes = CurlMetricUtils::getCurrentMetrics();
            $soapCallResponse = $client->__soapCall(
                $request->getFunction(),
                (array) SoapHelper::composeArguments($request),
                [],
                SoapHelper::composeRequestHeaders($request),
                $outputHeaders
            );
            $this->sendMetrics($request);

            return $this->handleResponse(
                $soapCallResponse,
                $client->__getLastResponseHeaders(),
                $outputHeaders,
                $request
            );

        } catch (SoapException $e) {
            $this->logger->error($e->getMessage(), ExceptionContextLoader::getContextForLogger($e));
            throw $e;
        } catch (Exception $e) {
            $this->logger->error(
                sprintf('Unknown exception: %s', $e->getMessage()),
                ExceptionContextLoader::getContextForLogger($e)
            );
            throw new SoapException('Unknown exception.', SoapException::UNKNOWN_EXCEPTION, $e);
        }
    }

    /**
     * @param mixed              $soapCallResponse
     * @param string             $lastResponseHeaders
     * @param array              $outputHeaders
     * @param RequestDtoAbstract $request
     *
     * @return ResponseDto
     */
    private function handleResponse(
        $soapCallResponse,
        ?string $lastResponseHeaders,
        ?array $outputHeaders,
        RequestDtoAbstract $request
    ): ResponseDto
    {
        $response = new ResponseDto($soapCallResponse, $lastResponseHeaders, $outputHeaders);

        if ($response->getResponseHeaderDto()) {
            /** @var ResponseHeaderDto $headers */
            $headers = $response->getResponseHeaderDto();
            $this->logger->debug(
                sprintf(
                    'Response: Status Code: %s, Reason Phrase: %s, Headers: %s, Body: %s',
                    $headers->getHttpStatusCode(),
                    $headers->getHttpReason(),
                    $response->getLastResponseHeaders(),
                    $response->getSoapCallResponse()
                )
            );
        } else {
            $this->logger->debug(sprintf('Response: Body: %s', $response->getSoapCallResponse()));
        }

        count([$request]);

        return $response;
    }

    /**
     * @param RequestDtoAbstract $request
     * @param array              $options
     *
     * @return array
     */
    private function composeOptions(RequestDtoAbstract $request, array $options): array
    {
        $options['connection_timeout'] = self::CONNECTION_TIMEOUT;
        $options['features']           = SOAP_WAIT_ONE_WAY_CALLS;
        $options['trace']              = TRUE;

        if ($request->getUser() || $request->getPassword()) {
            $options['login']    = $request->getUser();
            $options['password'] = $request->getPassword();
        }

        if ($request->getVersion()) {
            $options['soap_version'] = $request->getVersion();
        }

        //Disable certificate verification
        $options['stream_context'] = stream_context_create(
            [
                'ssl' => [
                    'verify_peer'       => FALSE,
                    'verify_peer_name'  => FALSE,
                    'allow_self_signed' => TRUE,
                ],
            ]
        );

        return $options;
    }

    /**
     * @param array $headers
     *
     * @return string
     */
    private function getHeadersAsString(array $headers): string
    {
        $string = '';
        foreach ($headers as $key => $value) {
            $string .= sprintf('%s: %s, ', $key, is_array($value) ? array_values($value)[0] : $value);
        }

        return mb_substr($string, 0, -2);
    }

    /**
     * @param RequestDtoAbstract $dto
     *
     * @throws CurlException
     */
    protected function sendMetrics(RequestDtoAbstract $dto): void
    {
        if ($this->metricsSender !== NULL) {
            $info  = $dto->getHeader()->getParams();
            $times = CurlMetricUtils::getTimes($this->startTimes);

            try {
                CurlMetricUtils::sendCurlMetrics(
                    $this->metricsSender->getSender(),
                    $times,
                    $info['node_id'][0] ?? NULL,
                    $info['correlation_id'][0] ?? NULL
                );
            } catch (Exception $e) {
                throw new CurlException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }

}
