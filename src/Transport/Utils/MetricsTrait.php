<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Utils;

use Exception;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;
use Hanaboso\CommonsBundle\Utils\CurlMetricUtils;

/**
 * Trait MetricsTrait
 *
 * @package Hanaboso\CommonsBundle\Transport\Utils
 */
trait MetricsTrait
{

    /**
     * @var MetricsSenderLoader|null
     */
    private $metricsSender;

    /**
     * @var array
     */
    private $startTimes = [];

    /**
     * @param RequestDto $dto
     *
     * @throws CurlException
     */
    protected function sendMetrics(RequestDto $dto): void
    {
        if ($this->metricsSender !== NULL) {
            $info  = $dto->getDebugInfo();
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
