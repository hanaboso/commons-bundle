<?php declare(strict_types=1);

namespace Tests\Unit\Metrics;

use Exception;
use Hanaboso\CommonsBundle\Metrics\InfluxDbSender;
use Hanaboso\CommonsBundle\Metrics\UDPSender;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class InfluxDbSenderTest
 *
 * @package Tests\Unit\Metrics
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
        $sender = $this->createPartialMock(UDPSender::class, ['send']);
        $sender->expects($this->any())->method('send')->willReturn(TRUE);

        $service = new InfluxDbSender($sender, 'test_measurement');

        $fields = ['foo' => 'bar', 'baz' => 10, 'bool' => TRUE, 'nil' => NULL];
        $tags   = ['environment' => 'test'];

        $result = $service->send($fields, $tags);
        $this->assertTrue($result);
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
        $sender = $this->createPartialMock(UDPSender::class, ['send']);
        $sender->expects($this->any())->method('send')->willReturn(TRUE);

        $service = new InfluxDbSender($sender, 'test_measurement');

        $fields = ['foo' => 'bar"s', 'baz' => 10, 'a' => 0, 'bool' => TRUE, 'nil' => NULL];
        $tags   = ['environment' => 'test', 'host' => 'localhost'];

        $message  = $service->createMessage($fields, $tags);
        $expected = 'test_measurement,environment=test,host=localhost foo="bar\"s",baz=10,a=0,bool=true,nil="null" ';

        // expected is appended by current timestamp
        $this->assertStringStartsWith($expected, $message);
        $this->assertEquals(strlen($expected) + 19, strlen($message));
    }

    /**
     * @covers InfluxDbSender::createMessage()
     * @throws Exception
     */
    public function testCreateMessageException(): void
    {
        /** @var MockObject|UDPSender $sender */
        $sender = $this->createPartialMock(UDPSender::class, ['send']);
        $sender->expects($this->any())->method('send')->willReturn(TRUE);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The fields must not be empty.');
        $service = new InfluxDbSender($sender, 'php_worker');
        $service->createMessage([]);
    }

}
