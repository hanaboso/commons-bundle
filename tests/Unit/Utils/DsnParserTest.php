<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Utils;

use Hanaboso\CommonsBundle\Utils\DsnParser;
use PHPUnit\Framework\TestCase;

/**
 * Class DsnParserTest
 *
 * @package CommonsBundleTests\Unit\Utils
 */
class DsnParserTest extends TestCase
{

    /**
     * @covers \Hanaboso\CommonsBundle\Utils\DsnParser::genericParser
     */
    public function testGenericParser(): void
    {
        $result = DsnParser::genericParser('http://guest:heslo@dev.company:1000/sss.qa');
        self::assertEquals(
            [
                'scheme' => 'http',
                'host'   => 'dev.company',
                'port'   => 1000,
                'user'   => 'guest',
                'pass'   => 'heslo',
                'path'   => '/sss.qa',
            ],
            $result
        );
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Utils\DsnParser::rabbitParser
     */
    public function testRabbitParser(): void
    {
        $result = DsnParser::rabbitParser('amqp://guest:heslo@dev.company:1000/sss.qa');
        self::assertEquals(
            [
                'username' => 'guest',
                'password' => 'heslo',
                'host'     => 'dev.company',
                'port'     => 1000,
                'vhost'    => 'sss.qa',
            ],
            $result
        );

        $result = DsnParser::rabbitParser('amqp://guest:heslo@dev.company/sss.qa');
        self::assertEquals(
            [
                'username' => 'guest',
                'password' => 'heslo',
                'host'     => 'dev.company',
                'port'     => '',
                'vhost'    => 'sss.qa',
            ],
            $result
        );

        $result = DsnParser::rabbitParser('amqp://guest:heslo@dev.company');
        self::assertEquals(
            [
                'username' => 'guest',
                'password' => 'heslo',
                'host'     => 'dev.company',
                'port'     => '',
                'vhost'    => '',
            ],
            $result
        );

        $result = DsnParser::rabbitParser('amqp://guest:heslo@dev.company?heartbeat=10&connection_timeout=10000');
        self::assertEquals(
            [
                'username'    => 'guest',
                'password'    => 'heslo',
                'host'        => 'dev.company',
                'port'        => '',
                'vhost'       => '',
                'queryParams' => [
                    'heartbeat'          => 10,
                    'connection_timeout' => 10000,
                ],
            ],
            $result
        );

        $result = DsnParser::rabbitParser('amqp://dev.company?heartbeat=10&connection_timeout=10000');
        self::assertEquals(
            [
                'host'        => 'dev.company',
                'port'        => '',
                'vhost'       => '',
                'queryParams' => [
                    'heartbeat'          => 10,
                    'connection_timeout' => 10000,
                ],
            ],
            $result
        );
    }

}