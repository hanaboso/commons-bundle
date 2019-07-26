<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Metrics\Impl;

use Doctrine\MongoDB\Connection;
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
     * @var Connection
     */
    private $conn;

    /**
     * @var string
     */
    private $collection;

    /**
     * MongoDbSender constructor.
     *
     * @param Connection $conn
     * @param string     $collection
     */
    public function __construct(Connection $conn, string $collection)
    {
        $this->conn       = $conn;
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

        /** @var array $res */
        $res = $this->conn->getMongoClient()->selectCollection('metrics', $this->collection)->insert($data);

        return ($res['err'] ?? NULL) === NULL;
    }

}