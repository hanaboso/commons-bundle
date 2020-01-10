<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Metrics\Impl;

use Exception;
use Hanaboso\CommonsBundle\Enum\MetricsEnum;
use Hanaboso\CommonsBundle\Metrics\Impl\InfluxDbSender;
use Hanaboso\CommonsBundle\Transport\Udp\UDPSender;
use PHPUnit\Framework\TestCase;

/**
 * Class InfluxDbSenderTest
 *
 * @package CommonsBundleTests\Integration\Metrics\Impl
 */
final class InfluxDbSenderTest extends TestCase
{

    /**
     * @throws Exception
     */
    public function testSend(): void
    {
        $sender = new InfluxDbSender(new UDPSender(), 'influxdb:8086', 'test');
        $res    = $sender->send(
            [
                MetricsEnum::REQUEST_TOTAL_DURATION => 123,
                MetricsEnum::CPU_USER_TIME          => 0,
                MetricsEnum::CPU_KERNEL_TIME        => 99,
            ],
            [
                MetricsEnum::HOST           => gethostname(),
                MetricsEnum::URI            => 'http://localhost.com',
                MetricsEnum::TOPOLOGY_ID    => '#999',
                MetricsEnum::CORRELATION_ID => '#456',
                MetricsEnum::NODE_ID        => '#123',
            ]
        );
        self::assertTrue($res);
    }

}
