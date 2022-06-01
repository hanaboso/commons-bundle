<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Monolog;

use CommonsBundleTests\KernelTestCaseAbstract;
use DateTimeImmutable;
use Hanaboso\CommonsBundle\Monolog\UdpHandler;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use Monolog\Level;
use Monolog\LogRecord;
use ReflectionException;

/**
 * Class UdpHandlerTest
 *
 * @package CommonsBundleTests\Unit\Monolog
 */
final class UdpHandlerTest extends KernelTestCaseAbstract
{

    use PrivateTrait;
    use CustomAssertTrait;

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\UdpHandler
     * @covers \Hanaboso\CommonsBundle\Monolog\UdpHandler::write
     * @throws ReflectionException
     */
    public function testUdpHandler(): void
    {
        $sender  = self::getContainer()->get('hbpf.transport.udp_sender');
        $handler = new UdpHandler($sender, 'host');

        $this->invokeMethod(
            $handler,
            'write',
            [new LogRecord(new DateTimeImmutable(), 'test', Level::Info, 'testMessage')],
        );
        self::assertFake();
    }

}
