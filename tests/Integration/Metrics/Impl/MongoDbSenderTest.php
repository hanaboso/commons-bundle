<?php declare(strict_types=1);

namespace Tests\Integration\Metrics\Impl;

use Doctrine\MongoDB\Connection;
use Exception;
use Hanaboso\CommonsBundle\Metrics\Impl\MongoDbSender;
use Tests\DatabaseTestCaseAbstract;

/**
 * Class MongoDbSenderTetst
 *
 * @package Tests\Integration\Metrics\Impl
 */
final class MongoDbSenderTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers MongoDbSender::send
     *
     * @throws Exception
     */
    public function testSend(): void
    {
        /** @var Connection $conn */
        $conn   = self::$container->get('doctrine_mongodb.odm.default_connection');
        $sender = new MongoDbSender($conn, 'test');
        $conn->selectCollection('metrics', 'test')->remove([]);

        self::assertTrue($sender->send(['asd' => '123'], ['a' => 'c']));
        self::assertTrue($sender->send(['asd' => 'qwe'], ['a' => 'b']));

        $res = (array) $conn->selectCollection('metrics', 'test')->count([]);
        self::assertEquals([2], $res);
    }

}
