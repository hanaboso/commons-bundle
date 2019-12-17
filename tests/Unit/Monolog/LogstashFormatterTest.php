<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Monolog;

use Exception;
use Hanaboso\CommonsBundle\Monolog\LogstashFormatter;
use Hanaboso\CommonsBundle\Utils\Json;
use PHPUnit\Framework\TestCase;

/**
 * Class LogstashFormatterTest
 *
 * @package CommonsBundleTests\Unit\Monolog
 */
final class LogstashFormatterTest extends TestCase
{

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
     * @covers LogstashFormatter::format()
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
                'timestamp' => 1505381163375,
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
     * @covers LogstashFormatter::format()
     */
    public function testFormatPipes(): void
    {
        $message = $this->logstashFormatter->format(
            [
                'message'    => 'Test message',
                'context'    => [
                    'correlation_id' => '123',
                    'node_id'        => '456',
                ],
                'level_name' => 'INFO',
                'channel'    => 'test',
            ]
        );

        $message = $this->correctMessage(Json::decode($message));

        self::assertEquals(
            [
                'timestamp'      => 1505381163375,
                'hostname'       => 'localhost',
                'type'           => 'test-service',
                'message'        => 'Test message',
                'channel'        => 'test',
                'severity'       => 'INFO',
                'correlation_id' => '123',
                'node_id'        => '456',
            ],
            $message
        );
    }

    /**
     * @covers LogstashFormatter::format()
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

        self::assertEquals(
            [
                'timestamp'  => 1505381163375,
                'hostname'   => 'localhost',
                'type'       => 'test-service',
                'message'    => 'Test message',
                'channel'    => 'test',
                'severity'   => 'INFO',
                'stacktrace' => [
                    'class'   => 'Exception',
                    'message' => 'Default exception',
                    'code'    => 0,
                    'file'    => '/var/www/tests/Unit/Monolog/LogstashFormatterTest.php:105',
                    'trace'   => '',
                ],
            ],
            $message
        );
    }

    /**
     * @covers LogstashFormatter::format()
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

        self::assertEquals(
            [
                'timestamp'      => 1505381163375,
                'hostname'       => 'localhost',
                'type'           => 'test-service',
                'message'        => 'Test message',
                'channel'        => 'test',
                'severity'       => 'INFO',
                'stacktrace'     => [
                    'class'   => 'Exception',
                    'message' => 'Default exception',
                    'code'    => 0,
                    'file'    => '/var/www/tests/Unit/Monolog/LogstashFormatterTest.php:145',
                    'trace'   => '',
                ],
                'correlation_id' => '123',
                'node_id'        => '456',
            ],
            $message
        );
    }

    /**
     * @param mixed[] $message
     *
     * @return mixed[]
     */
    private function correctMessage(array $message): array
    {
        $message['timestamp'] = 1505381163375;
        $message['hostname']  = 'localhost';

        if (isset($message['stacktrace']['trace'])) {
            $message['stacktrace']['trace'] = '';
        }

        return $message;
    }

    /**
     * @covers LogstashFormatter::format()
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
                'timestamp'     => 1505381163375,
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

}
