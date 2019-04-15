<?php declare(strict_types=1);

namespace Tests\Unit\Metrics;

use Hanaboso\CommonsBundle\Metrics\SystemMetrics;
use PHPUnit\Framework\TestCase;

/**
 * Class SystemMetricsTest
 *
 * @package Tests\Unit\Metrics
 */
final class SystemMetricsTest extends TestCase
{

    /**
     * @covers SystemMetrics::getCurrentTimestamp()
     */
    public function testGetCurrentTimestamp(): void
    {
        $ts = SystemMetrics::getCurrentTimestamp();
        self::assertTrue(is_numeric($ts));

        $ts2 = SystemMetrics::getCurrentTimestamp();
        self::assertGreaterThanOrEqual($ts, $ts2);
    }

    /**
     * @covers SystemMetrics::getCpuTimes()
     */
    public function testGetCpuTimes(): void
    {
        $before = SystemMetrics::getCpuTimes();
        self::assertArrayHasKey(SystemMetrics::CPU_TIME_USER, $before);
        self::assertArrayHasKey(SystemMetrics::CPU_TIME_KERNEL, $before);
        self::assertArrayHasKey(SystemMetrics::CPU_START_TIME, $before);
        self::assertGreaterThan(0, $before[SystemMetrics::CPU_TIME_USER]);
        self::assertGreaterThanOrEqual(0, $before[SystemMetrics::CPU_TIME_KERNEL]);
        self::assertGreaterThan(0, $before[SystemMetrics::CPU_START_TIME]);

        $cpuUsageBefore = SystemMetrics::getCpuUsage();
        self::assertGreaterThan(0, $cpuUsageBefore);
    }

}
