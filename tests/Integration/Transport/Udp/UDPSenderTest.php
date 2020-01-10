<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Transport\Udp;

use Exception;
use Hanaboso\CommonsBundle\Transport\Udp\UDPSender;
use PHPUnit\Framework\TestCase;

/**
 * Class UDPSenderTest
 *
 * @package CommonsBundleTests\Integration\Transport\Udp
 */
final class UDPSenderTest extends TestCase
{

    private const LIMIT = 20;

    /**
     * Test whether resolving ip address returns the ip address string or empty string if cannot be resolved.
     * Also tests if the resolving of invalid host does not take too long.
     *
     * @covers \Hanaboso\CommonsBundle\Transport\Udp\UDPSender::refreshIp()
     *
     * @throws Exception
     */
    public function testRefreshIp(): void
    {
        $start = microtime(TRUE);

        $sender = new UDPSender();
        $ip     = $sender->refreshIp('localhost');
        self::assertEquals('127.0.0.1', $ip);

        $ip = $sender->refreshIp('localhost');
        self::assertEquals('127.0.0.1', $ip);

        $sender = new UDPSender();
        $ip     = $sender->refreshIp('google.com');
        self::assertCount(4, explode('.', $ip));

        $sender = new UDPSender();
        $ip     = $sender->refreshIp('invalidhostname');
        self::assertEquals('', $ip);

        $end = microtime(TRUE);
        self::assertLessThanOrEqual(self::LIMIT, $end - $start);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Udp\UDPSender::send()
     *
     * @throws Exception
     */
    public function testSend(): void
    {
        $start = microtime(TRUE);

        $message = 'abc,name=def,host=ghi key1=val1,key2=val2 1465839830100400200';

        $sender = new UDPSender();
        $result = $sender->send('localhost:61999', $message);
        self::assertTrue($result);

        $sender = new UDPSender();
        $result = $sender->send('invalidhost:61999', $message);
        self::assertFalse($result);

        // here we cannot assert result because we don't know if influxdb host exists
        // but we can check if packets are delivered right in influxdb container using tcpdump or similar tool
        $sender = new UDPSender();
        $sender->send('influxdb:61999', $message);

        // Check if sending is not delaying too much
        $end = microtime(TRUE);
        self::assertLessThanOrEqual(self::LIMIT, $end - $start);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Udp\UDPSender::send()
     *
     * @throws Exception
     */
    public function testSendManyOnNonExistingHost(): void
    {
        $start = microtime(TRUE);

        $message = 'abc,name=def,host=ghi key1=val1,key2=val2 1465839830100400200';
        $sender  = new UDPSender();

        for ($i = 0; $i < 1_000; $i++) {
            $result = $sender->send('invalidhost:61999', $message);
            self::assertFalse($result);
        }

        // Check if sending is not delaying too much
        $end = microtime(TRUE);
        self::assertLessThanOrEqual(self::LIMIT, $end - $start);
    }

}
