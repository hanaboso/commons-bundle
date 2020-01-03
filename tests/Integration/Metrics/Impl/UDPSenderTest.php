<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Metrics\Impl;

use Exception;
use Hanaboso\CommonsBundle\Metrics\Impl\UDPSender;
use PHPUnit\Framework\TestCase;

/**
 * Class UDPSenderTest
 *
 * @package CommonsBundleTests\Integration\Metrics\Impl
 */
final class UDPSenderTest extends TestCase
{

    private const LIMIT = 20;

    /**
     * Test whether resolving ip address returns the ip address string or empty string if cannot be resolved.
     * Also tests if the resolving of invalid host does not take too long.
     *
     * @covers \Hanaboso\CommonsBundle\Metrics\Impl\UDPSender::refreshIp()
     *
     * @throws Exception
     */
    public function testRefreshIp(): void
    {
        $start = microtime(TRUE);

        $sender = new UDPSender('localhost', 61_999);
        $ip     = $sender->refreshIp();
        self::assertEquals('127.0.0.1', $ip);

        $ip = $sender->refreshIp();
        self::assertEquals('127.0.0.1', $ip);

        $sender = new UDPSender('google.com', 61_999);
        $ip     = $sender->refreshIp();
        self::assertCount(4, explode('.', $ip));

        $sender = new UDPSender('invalidhostname', 61_999);
        $ip     = $sender->refreshIp();
        self::assertEquals('', $ip);

        $end = microtime(TRUE);
        self::assertLessThanOrEqual(self::LIMIT, $end - $start);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Metrics\Impl\UDPSender::send()
     *
     * @throws Exception
     */
    public function testSend(): void
    {
        $start = microtime(TRUE);

        $message = 'abc,name=def,host=ghi key1=val1,key2=val2 1465839830100400200';

        $sender = new UDPSender('localhost', 61_999);
        $result = $sender->send($message);
        self::assertTrue($result);

        $sender = new UDPSender('invalidhost', 61_999);
        $result = $sender->send($message);
        self::assertFalse($result);

        // here we cannot assert result because we don't know if influxdb host exists
        // but we can check if packets are delivered right in influxdb container using tcpdump or similar tool
        $sender = new UDPSender('influxdb', 61_999);
        $sender->send($message);

        // Check if sending is not delaying too much
        $end = microtime(TRUE);
        self::assertLessThanOrEqual(self::LIMIT, $end - $start);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Metrics\Impl\UDPSender::send()
     *
     * @throws Exception
     */
    public function testSendManyOnNonExistingHost(): void
    {
        $start = microtime(TRUE);

        $message = 'abc,name=def,host=ghi key1=val1,key2=val2 1465839830100400200';
        $sender  = new UDPSender('invalidhost', 61_999);

        for ($i = 0; $i < 1_000; $i++) {
            $result = $sender->send($message);
            self::assertFalse($result);
        }

        // Check if sending is not delaying too much
        $end = microtime(TRUE);
        self::assertLessThanOrEqual(self::LIMIT, $end - $start);
    }

}
