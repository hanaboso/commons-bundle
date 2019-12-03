<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Metrics\Impl;

use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderInterface;
use Hanaboso\CommonsBundle\Utils\DateTimeUtils;

/**
 * Class MongoDbSender
 *
 * @package Hanaboso\CommonsBundle\Metrics\Impl
 */
final class MongoDbSender implements MetricsSenderInterface
{

    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @var string
     */
    private $collection;

    /**
     * MongoDbSender constructor.
     *
     * @param DocumentManager $dm
     * @param string          $collection
     */
    public function __construct(DocumentManager $dm, string $collection)
    {
        $this->dm         = $dm;
        $this->collection = $collection;
    }

    /**
     * @param array $fields
     * @param array $tags
     *
     * @return bool
     * @throws Exception
     */
    public function send(array $fields, array $tags = []): bool
    {
        $fields['created'] = DateTimeUtils::getUtcDateTime()->getTimestamp();

        $data = [
            'fields' => $fields,
            'tags'   => $tags,
        ];

        $db  = $this->dm->getConfiguration()->getDefaultDB();
        $res = $this->dm->getClient()->selectCollection((string) $db, $this->collection)->insertOne([$data]);

        return $res->isAcknowledged();
    }

}