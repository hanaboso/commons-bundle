<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Udp;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Transport\Udp\UDPSender;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use Hanaboso\Utils\Exception\DateTimeException;
use phpmock\phpunit\PHPMock;
use ReflectionException;

/**
 * Class UdpSenderTest
 *
 * @package CommonsBundleTests\Unit\Transport\Udp
 */
final class UdpSenderTest extends KernelTestCaseAbstract
{

    use PHPMock;
    use PrivateTrait;

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Udp\UDPSender::getSocket
     *
     * @throws ReflectionException
     */
    public function testGetSocket(): void
    {
        $this->getFunctionMock('Hanaboso\CommonsBundle\Transport\Udp', 'socket_create')
            ->expects(self::any())
            ->willReturn(FALSE);

        $this->getFunctionMock('Hanaboso\CommonsBundle\Transport\Udp', 'socket_last_error')
            ->expects(self::any())
            ->willReturn(5);

        $sender = new UDPSender();
        $this->setProperty($sender, 'socket', '1');

        $this->invokeMethod($sender, 'getSocket');

        self::assertTrue(TRUE);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Udp\UDPSender::send
     *
     * @throws DateTimeException
     */
    public function testSendErr(): void
    {
        $message = 'abc,name=def,host=ghi key1=val1,key2=val2 1465839830100400200';

        $this->getFunctionMock('Hanaboso\CommonsBundle\Transport\Udp', 'socket_sendto')
            ->expects(self::any())
            ->willReturn(FALSE);

        $sender = new UDPSender();
        $result = $sender->send('influxdb:61999', $message);
        self::assertFalse($result);
    }

}