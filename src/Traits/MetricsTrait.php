<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Traits;

use Exception;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;
use Hanaboso\CommonsBundle\Utils\CurlMetricUtils;
use Hanaboso\Utils\System\PipesHeaders;

/**
 * Trait MetricsTrait
 *
 * @package Hanaboso\CommonsBundle\Traits
 */
trait MetricsTrait
{

    /**
     * @var MetricsSenderLoader|null
     */
    private ?MetricsSenderLoader $metricsSender;

    /**
     * @var mixed[]
     */
    private array $startTimes;

    /**
     * @param RequestDto  $dto
     * @param int         $responseCode
     * @param string|null $responseBody
     *
     * @throws CurlException
     */
    protected function sendMetrics(RequestDto $dto, int $responseCode, ?string $responseBody): void
    {
        if ($this->metricsSender !== NULL) {
            $info  = $dto->getDebugInfo();
            $times = CurlMetricUtils::getTimes($this->startTimes);

            try {
                CurlMetricUtils::sendCurlMetrics(
                    $this->metricsSender->getSender(),
                    $times,
                    $info[PipesHeaders::NODE_ID] ?? NULL,
                    $info[PipesHeaders::NODE_NAME] ?? NULL,
                    $info[PipesHeaders::TOPOLOGY_ID] ?? NULL,
                    $info[PipesHeaders::CORRELATION_ID] ?? NULL,
                    $info[PipesHeaders::USER] ?? NULL,
                    $info[PipesHeaders::APPLICATION] ?? NULL,
                    $dto->getUriString(),
                    $responseCode,
                    $responseBody,
                );
            } catch (Exception $e) {
                throw new CurlException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }

}
