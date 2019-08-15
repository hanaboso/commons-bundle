<?php declare(strict_types=1);

namespace Tests\Integration\Metrics\Impl;

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
        $sender = new MongoDbSender(self::$container->get('doctrine_mongodb.odm.metrics_document_manager'), 'test');
        $this->dm->getConnection()->selectCollection('metrics', 'test')->remove([]);

        self::assertTrue($sender->send(['asd' => '123'], ['a' => 'c']));
        self::assertTrue($sender->send(['asd' => 'qwe'], ['a' => 'b']));

        $res = (array) $this->dm->getConnection()->selectCollection('metrics', 'test')->count([]);
        self::assertEquals([2], $res);
    }

}
