<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Monolog;

use DateTimeImmutable;
use Exception;
use Hanaboso\CommonsBundle\Monolog\LogstashFormatter;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use Hanaboso\Utils\String\Json;
use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;
use SoapFault;

/**
 * Class LogstashFormatterTest
 *
 * @package CommonsBundleTests\Unit\Monolog
 */
final class LogstashFormatterTest extends TestCase
{

    use PrivateTrait;

    /**
     * @var LogstashFormatter
     */
    private LogstashFormatter $logstashFormatter;

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LogstashFormatter::format
     *
     * @throws Exception
     */
    public function testFormat(): void
    {
        $message = $this->logstashFormatter->format(
            new LogRecord(new DateTimeImmutable(), 'test', Level::Info, 'Test message'),
        );

        $message = $this->correctMessage(Json::decode($message));

        self::assertEquals(
            [
                'channel'   => 'test',
                'hostname'  => 'localhost',
                'message'   => 'Test message',
                'severity'  => 'INFO',
                'timestamp' => 1_505_381_163_375,
                'type'      => 'test-service',
            ],
            $message,
        );
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LogstashFormatter::format
     *
     * @throws Exception
     */
    public function testFormatPipes(): void
    {
        $message = $this->logstashFormatter->format(
            new LogRecord(
                new DateTimeImmutable(),
                'test',
                Level::Info,
                'Test message',
                [
                    'correlation_id' => '123',
                    'node_id'        => '456',
                    'node_name'      => 'name',
                    'topology_id'    => '1',
                ],
            ),
        );

        $message = $this->correctMessage(Json::decode($message));

        self::assertEquals(
            [
                'channel'        => 'test',
                'correlation_id' => '123',
                'hostname'       => 'localhost',
                'message'        => 'Test message',
                'node_id'        => '456',
                'node_name'      => 'name',
                'severity'       => 'INFO',
                'timestamp'      => 1_505_381_163_375,
                'topology_id'    => '1',
                'type'           => 'test-service',
            ],
            $message,
        );
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LogstashFormatter::format
     *
     * @throws Exception
     */
    public function testFormatException(): void
    {
        $message = $this->logstashFormatter->format(
            new LogRecord(
                new DateTimeImmutable(),
                'test',
                Level::Info,
                'Test message',
                [
                    'exception' => new Exception('Default exception'),
                ],
            ),
        );

        $message = $this->correctMessage(Json::decode($message));

        self::assertEquals(7, count($message));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LogstashFormatter::format
     *
     * @throws Exception
     */
    public function testFormatExceptionPipes(): void
    {
        $message = $this->logstashFormatter->format(
            new LogRecord(
                new DateTimeImmutable(),
                'test',
                Level::Info,
                'Test message',
                [
                    'correlation_id' => '123',
                    'exception'      => new Exception('Default exception'),
                    'node_id'        => '456',
                ],
            ),
        );

        $message = $this->correctMessage(Json::decode($message));

        self::assertEquals(9, count($message));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LogstashFormatter::format
     *
     * @throws Exception
     */
    public function testContext(): void
    {
        $message = $this->logstashFormatter->format(
            new LogRecord(
                new DateTimeImmutable(),
                'test',
                Level::Info,
                'Test message',
                [
                    'topology_name' => 'topology_1',
                    'type'          => 'starting_point',
                ],
            ),
        );

        $message = $this->correctMessage(Json::decode($message));

        self::assertEquals(
            [
                'channel'       => 'test',
                'hostname'      => 'localhost',
                'message'       => 'Test message',
                'severity'      => 'INFO',
                'timestamp'     => 1_505_381_163_375,
                'topology_name' => 'topology_1',
                'type'          => 'starting_point',
            ],
            $message,
        );
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LogstashFormatter::normalizeException
     *
     * @throws Exception
     */
    public function testNormalizeException(): void
    {
        $exception = new SoapFault('100', 'string', 'actor', 'detail');
        $result    = $this->invokeMethod($this->logstashFormatter, 'normalizeException', [$exception]);

        self::assertEquals('detail', $result['detail']);
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->logstashFormatter = new LogstashFormatter('test-service');
    }

    /**
     * @param mixed[] $message
     *
     * @return mixed[]
     */
    private function correctMessage(array $message): array
    {
        $message['timestamp'] = 1_505_381_163_375;
        $message['hostname']  = 'localhost';

        if (isset($message['stacktrace']['trace'])) {
            $message['stacktrace']['trace'] = '';
        }

        return $message;
    }

}
