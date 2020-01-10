<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Utils;

use Exception;
use Hanaboso\CommonsBundle\Enum\MetricsEnum;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderInterface;
use Hanaboso\Utils\System\SystemUsage;

/**
 * Class CurlMetricUtils
 *
 * @package Hanaboso\CommonsBundle\Utils
 */
final class CurlMetricUtils
{

    public const KEY_TIMESTAMP        = 'timestamp';
    public const KEY_CPU              = 'cpu';
    public const KEY_REQUEST_DURATION = 'request_duration';
    public const KEY_USER_TIME        = 'user_time';
    public const KEY_KERNEL_TIME      = 'kernel_time';

    /**
     * @param mixed[] $startMetrics
     *
     * @return mixed[]
     */
    public static function getTimes(array $startMetrics): array
    {
        $startTime      = $startMetrics[self::KEY_TIMESTAMP];
        $startCpuUser   = $startMetrics[self::KEY_CPU][SystemUsage::CPU_TIME_USER];
        $startCpuKernel = $startMetrics[self::KEY_CPU][SystemUsage::CPU_TIME_KERNEL];

        $endMetrics = self::getCurrentMetrics();

        return [
            self::KEY_REQUEST_DURATION => $endMetrics[self::KEY_TIMESTAMP] - $startTime,
            self::KEY_USER_TIME        => $endMetrics[self::KEY_CPU][SystemUsage::CPU_TIME_USER] - $startCpuUser,
            self::KEY_KERNEL_TIME      => $endMetrics[self::KEY_CPU][SystemUsage::CPU_TIME_KERNEL] - $startCpuKernel,
        ];
    }

    /**
     * @param MetricsSenderInterface $sender
     * @param mixed[]                $timeData
     * @param string|null            $nodeId
     * @param string|null            $correlationId
     *
     * @throws Exception
     */
    public static function sendCurlMetrics(
        MetricsSenderInterface $sender,
        array $timeData,
        ?string $nodeId = NULL,
        ?string $correlationId = NULL
    ): void
    {
        $info = [];

        if ($nodeId) {
            $info[MetricsEnum::NODE_ID] = $nodeId;
        }

        if ($correlationId) {
            $info[MetricsEnum::CORRELATION_ID] = $correlationId;
        }

        $sender->send(
            [
                MetricsEnum::REQUEST_TOTAL_DURATION_SENT => $timeData[self::KEY_REQUEST_DURATION],
            ],
            $info
        );
    }

    /**
     * @return mixed[]
     */
    public static function getCurrentMetrics(): array
    {
        return [
            self::KEY_TIMESTAMP => SystemUsage::getCurrentTimestamp(),
            self::KEY_CPU       => SystemUsage::getCpuTimes(),
        ];
    }

}
