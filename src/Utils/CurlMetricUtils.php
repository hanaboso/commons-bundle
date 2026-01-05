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

    public const string KEY_TIMESTAMP        = 'timestamp';
    public const string KEY_CPU              = 'cpu';
    public const string KEY_REQUEST_DURATION = 'request_duration';
    public const string KEY_USER_TIME        = 'user_time';
    public const string KEY_KERNEL_TIME      = 'kernel_time';

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
            self::KEY_KERNEL_TIME      => $endMetrics[self::KEY_CPU][SystemUsage::CPU_TIME_KERNEL] - $startCpuKernel,
            self::KEY_REQUEST_DURATION => $endMetrics[self::KEY_TIMESTAMP] - $startTime,
            self::KEY_USER_TIME        => $endMetrics[self::KEY_CPU][SystemUsage::CPU_TIME_USER] - $startCpuUser,
        ];
    }

    /**
     * @param MetricsSenderInterface $sender
     * @param mixed[]                $timeData
     * @param string|null            $nodeId
     * @param string|null            $topologyId
     * @param string|null            $correlationId
     * @param string|null            $user
     * @param string|null            $application
     * @param int|null               $responseCode
     * @param string|null            $responseError
     *
     * @throws Exception
     */
    public static function sendCurlMetrics(
        MetricsSenderInterface $sender,
        array $timeData,
        ?string $nodeId = NULL,
        ?string $topologyId = NULL,
        ?string $correlationId = NULL,
        ?string $user = NULL,
        ?string $application = NULL,
        ?int $responseCode = NULL,
        ?string $responseError = NULL,
    ): void
    {
        $info = [];

        if ($user) {
            $info[MetricsEnum::USER_ID->value] = $user;
        }

        if ($application) {
            $info[MetricsEnum::APPLICATION_ID->value] = $application;
        }

        if ($nodeId) {
            $info[MetricsEnum::NODE_ID->value] = $nodeId;
        }

        if ($topologyId) {
            $info[MetricsEnum::TOPOLOGY_ID->value] = $topologyId;
        }

        if ($correlationId) {
            $info[MetricsEnum::CORRELATION_ID->value] = $correlationId;
        }

        $fields = [
            MetricsEnum::REQUEST_RESPONSE_CODE->value       => $responseCode,
            MetricsEnum::REQUEST_TOTAL_DURATION_SENT->value => $timeData[self::KEY_REQUEST_DURATION],
        ];

        if ($responseError) {
            $fields = array_merge($fields, [MetricsEnum::REQUEST_RESPONSE_ERROR->value => $responseError]);
        }

        $sender->send($fields, $info, FALSE);
    }

    /**
     * @return mixed[]
     */
    public static function getCurrentMetrics(): array
    {
        return [
            self::KEY_CPU       => SystemUsage::getCpuTimes(),
            self::KEY_TIMESTAMP => SystemUsage::getCurrentTimestamp(),
        ];
    }

}
