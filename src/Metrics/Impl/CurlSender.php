<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Metrics\Impl;

use Hanaboso\CommonsBundle\Metrics\MetricsSenderInterface;
use Hanaboso\CommonsBundle\WorkerApi\Client;
use Hanaboso\Utils\Date\DateTimeUtils;
use Throwable;

/**
 * Class CurlSender
 *
 * @package Hanaboso\CommonsBundle\Metrics\Impl
 */
final class CurlSender implements MetricsSenderInterface
{

    /**
     * CurlSender constructor.
     *
     * @param Client $client
     */
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param mixed[] $fields
     * @param mixed[] $tags
     * @param bool    $isProcessMetrics
     *
     * @return bool
     */
    public function send(array $fields, array $tags = [], bool $isProcessMetrics = TRUE): bool
    {
        try {
            $fields['created'] = DateTimeUtils::getUtcDateTime()->getTimestamp();

            $data = [
                'fields' => $fields,
                'tags'   => $tags,
            ];

            $res = $this->client->send(
                sprintf(
                    'metrics/%s',
                    $isProcessMetrics ? 'monolith' : 'connectors',
                ),
                $data,
            );

            return $res->getStatusCode() === 200;
        } catch (Throwable) {
            return FALSE;
        }
    }

}
