<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Metrics\Impl;

use Exception;
use Hanaboso\CommonsBundle\Metrics\Impl\InfluxDbSender;
use Hanaboso\CommonsBundle\Transport\Udp\UDPSender;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class InfluxDbSenderTest
 *
 * @package CommonsBundleTests\Unit\Metrics\Impl
 */
final class InfluxDbSenderTest extends TestCase
{

    /**
     * @covers \Hanaboso\CommonsBundle\Metrics\Impl\InfluxDbSender::send()
     *
     * @throws Exception
     */
    public function testSend(): void
    {
        /** @var MockObject|UDPSender $sender */
        $sender = self::createPartialMock(UDPSender::class, ['send']);
        $sender->expects(self::any())->method('send')->willReturn(TRUE);

        $service = new InfluxDbSender($sender, 'host:5100', 'test_measurement');

        $fields = ['foo' => 'bar', 'baz' => 10, 'bool' => TRUE, 'nil' => NULL];
        $tags   = ['environment' => 'test'];

        $result = $service->send($fields, $tags);
        self::assertTrue($result);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Metrics\Impl\InfluxDbSender::createMessage()
     * @covers \Hanaboso\CommonsBundle\Metrics\Impl\InfluxDbSender::join()
     * @covers \Hanaboso\CommonsBundle\Metrics\Impl\InfluxDbSender::prepareTags()
     * @covers \Hanaboso\CommonsBundle\Metrics\Impl\InfluxDbSender::prepareFields()
     * @covers \Hanaboso\CommonsBundle\Metrics\Impl\InfluxDbSender::escapeFieldValue()
     *
     * @throws Exception
     */
    public function testCreateMessage(): void
    {
        /** @var MockObject|UDPSender $sender */
        $sender = self::createPartialMock(UDPSender::class, ['send']);
        $sender->expects(self::any())->method('send')->willReturn(TRUE);

        $service = new InfluxDbSender($sender, 'host:5100', 'test_measurement');

        $fields = ['foo' => 'bar"s', 'baz' => 10, 'a' => 0, 'bool' => TRUE, 'nil' => NULL];
        $tags   = ['environment' => 'test', 'host' => 'localhost'];

        $message  = $service->createMessage($fields, $tags);
        $expected = 'test_measurement,environment=test,host=localhost foo="bar\"s",baz=10,a=0,bool=true,nil="null" ';

        // expected is appended by current timestamp
        self::assertStringStartsWith($expected, $message);
        self::assertEquals(strlen($expected) + 19, strlen($message));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Metrics\Impl\InfluxDbSender::createMessage()
     *
     * @throws Exception
     */
    public function testCreateMessageException(): void
    {
        /** @var MockObject|UDPSender $sender */
        $sender = self::createPartialMock(UDPSender::class, ['send']);
        $sender->expects(self::any())->method('send')->willReturn(TRUE);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('The fields must not be empty.');
        $service = new InfluxDbSender($sender, 'host:5100', 'php_worker');
        $service->createMessage([]);
    }

}
