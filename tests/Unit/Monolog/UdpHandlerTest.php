<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Monolog;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Monolog\UdpHandler;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use ReflectionException;

/**
 * Class UdpHandlerTest
 *
 * @package CommonsBundleTests\Unit\Monolog
 */
final class UdpHandlerTest extends KernelTestCaseAbstract
{

    use PrivateTrait;

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\UdpHandler
     * @covers \Hanaboso\CommonsBundle\Monolog\UdpHandler::write
     * @throws ReflectionException
     */
    public function testUdpHandler(): void
    {
        $sender  = self::getContainer()->get('hbpf.transport.udp_sender');
        $handler = new UdpHandler($sender, 'host');

        $this->invokeMethod($handler, 'write', [['formatted' => 'message']]);
        self::assertTrue(TRUE);
    }

}
