<?php declare(strict_types=1);

namespace Tests\Unit\Metrics\Impl;

use Exception;
use Hanaboso\CommonsBundle\Metrics\Impl\InfluxDbSender;
use Hanaboso\CommonsBundle\Metrics\Impl\UDPSender;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class InfluxDbSenderTest
 *
 * @package Tests\Unit\Metrics\Impl
 */
final class InfluxDbSenderTest extends TestCase
{

    /**
     * @covers InfluxDbSender::send()
     * @throws Exception
     */
    public function testSend(): void
    {
        /** @var MockObject|UDPSender $sender */
        $sender = self::createPartialMock(UDPSender::class, ['send']);
        $sender->expects(self::any())->method('send')->willReturn(TRUE);

        $service = new InfluxDbSender($sender, 'test_measurement');

        $fields = ['foo' => 'bar', 'baz' => 10, 'bool' => TRUE, 'nil' => NULL];
        $tags   = ['environment' => 'test'];

        $result = $service->send($fields, $tags);
        self::assertTrue($result);
    }

    /**
     * @covers InfluxDbSender::createMessage()
     * @covers InfluxDbSender::join()
     * @covers InfluxDbSender::prepareTags()
     * @covers InfluxDbSender::prepareFields()
     * @covers InfluxDbSender::escapeFieldValue()
     * @throws Exception
     */
    public function testCreateMessage(): void
    {
        /** @var MockObject|UDPSender $sender */
        $sender = self::createPartialMock(UDPSender::class, ['send']);
        $sender->expects(self::any())->method('send')->willReturn(TRUE);

        $service = new InfluxDbSender($sender, 'test_measurement');

        $fields = ['foo' => 'bar"s', 'baz' => 10, 'a' => 0, 'bool' => TRUE, 'nil' => NULL];
        $tags   = ['environment' => 'test', 'host' => 'localhost'];

        $message  = $service->createMessage($fields, $tags);
        $expected = 'test_measurement,environment=test,host=localhost foo="bar\"s",baz=10,a=0,bool=true,nil="null" ';

        // expected is appended by current timestamp
        self::assertStringStartsWith($expected, $message);
        self::assertEquals(strlen($expected) + 19, strlen($message));
    }

    /**
     * @covers InfluxDbSender::createMessage()
     * @throws Exception
     */
    public function testCreateMessageException(): void
    {
        /** @var MockObject|UDPSender $sender */
        $sender = self::createPartialMock(UDPSender::class, ['send']);
        $sender->expects(self::any())->method('send')->willReturn(TRUE);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('The fields must not be empty.');
        $service = new InfluxDbSender($sender, 'php_worker');
        $service->createMessage([]);
    }

}