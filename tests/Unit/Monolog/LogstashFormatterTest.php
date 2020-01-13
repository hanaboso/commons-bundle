<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Monolog;

use Exception;
use Hanaboso\CommonsBundle\Monolog\LogstashFormatter;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use Hanaboso\Utils\String\Json;
use PHPUnit\Framework\TestCase;
use ReflectionException;
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
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->logstashFormatter = new LogstashFormatter('test-service');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LogstashFormatter::format()
     */
    public function testFormat(): void
    {
        $message = $this->logstashFormatter->format(
            [
                'message'    => 'Test message',
                'context'    => [],
                'level_name' => 'INFO',
                'channel'    => 'test',
            ]
        );

        $message = $this->correctMessage(Json::decode($message));

        self::assertEquals(
            [
                'timestamp' => 1_505_381_163_375,
                'hostname'  => 'localhost',
                'type'      => 'test-service',
                'message'   => 'Test message',
                'channel'   => 'test',
                'severity'  => 'INFO',
            ],
            $message
        );
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LogstashFormatter::format()
     */
    public function testFormatPipes(): void
    {
        $message = $this->logstashFormatter->format(
            [
                'message'    => 'Test message',
                'context'    => [
                    'correlation_id' => '123',
                    'node_id'        => '456',
                    'node_name'      => 'name',
                    'topology_id'    => '1',
                ],
                'level_name' => 'INFO',
                'channel'    => 'test',
            ]
        );

        $message = $this->correctMessage(Json::decode($message));

        self::assertEquals(
            [
                'timestamp'      => 1_505_381_163_375,
                'hostname'       => 'localhost',
                'type'           => 'test-service',
                'message'        => 'Test message',
                'channel'        => 'test',
                'severity'       => 'INFO',
                'correlation_id' => '123',
                'node_id'        => '456',
                'node_name'      => 'name',
                'topology_id'    => '1',
            ],
            $message
        );
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LogstashFormatter::format()
     */
    public function testFormatException(): void
    {
        $message = $this->logstashFormatter->format(
            [
                'message'    => 'Test message',
                'context'    => [
                    'exception' => new Exception('Default exception'),
                ],
                'level_name' => 'INFO',
                'channel'    => 'test',
            ]
        );

        $message = $this->correctMessage(Json::decode($message));

        self::assertEquals(7, count($message));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LogstashFormatter::format()
     */
    public function testFormatExceptionPipes(): void
    {
        $message = $this->logstashFormatter->format(
            [
                'message'    => 'Test message',
                'context'    => [
                    'correlation_id' => '123',
                    'node_id'        => '456',
                    'exception'      => new Exception('Default exception'),
                ],
                'level_name' => 'INFO',
                'channel'    => 'test',
            ]
        );

        $message = $this->correctMessage(Json::decode($message));

        self::assertEquals(9, count($message));
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

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LogstashFormatter::format()
     */
    public function testContext(): void
    {
        $message = $this->logstashFormatter->format(
            [
                'message'    => 'Test message',
                'context'    => [
                    'type'          => 'starting_point',
                    'topology_name' => 'topology_1',
                ],
                'level_name' => 'INFO',
                'channel'    => 'test',
            ]
        );

        $message = $this->correctMessage(Json::decode($message));

        self::assertEquals(
            [
                'timestamp'     => 1_505_381_163_375,
                'hostname'      => 'localhost',
                'type'          => 'starting_point',
                'message'       => 'Test message',
                'channel'       => 'test',
                'severity'      => 'INFO',
                'topology_name' => 'topology_1',
            ],
            $message
        );
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LogstashFormatter::normalizeException
     *
     * @throws ReflectionException
     */
    public function testNormalizeException(): void
    {
        $exception = new SoapFault('100', 'string', 'actor', 'detail');
        $result    = $this->invokeMethod($this->logstashFormatter, 'normalizeException', [$exception]);

        self::assertEquals('detail', $result['detail']);
    }

}
