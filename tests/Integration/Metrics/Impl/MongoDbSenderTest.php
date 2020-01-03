<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Metrics\Impl;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
use Hanaboso\CommonsBundle\Metrics\Impl\MongoDbSender;

/**
 * Class MongoDbSenderTest
 *
 * @package CommonsBundleTests\Integration\Metrics\Impl
 */
final class MongoDbSenderTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Metrics\Impl\MongoDbSender::send
     *
     * @throws Exception
     */
    public function testSend(): void
    {
        /** @var DocumentManager $dm */
        $dm     = self::$container->get('doctrine_mongodb.odm.metrics_document_manager');
        $sender = new MongoDbSender($dm, 'test');
        $this->dm->getClient()->dropDatabase('metrics');

        self::assertTrue($sender->send(['asd' => '123'], ['a' => 'c']));
        self::assertTrue($sender->send(['asd' => 'qwe'], ['a' => 'b']));

        $res = (array) $this->dm->getClient()->selectCollection('metrics', 'test')->count([]);
        self::assertEquals([2], $res);
    }

}
