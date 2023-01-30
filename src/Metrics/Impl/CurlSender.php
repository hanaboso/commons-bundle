<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Metrics\Impl;

use GuzzleHttp\Psr7\Request;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderInterface;
use Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\Utils\Date\DateTimeUtils;
use Hanaboso\Utils\String\Json;
use Throwable;

/**
 * Class CurlSender
 *
 * @package Hanaboso\CommonsBundle\Metrics\Impl
 */
final class CurlSender implements MetricsSenderInterface
{

    /**
     * @var int
     */
    private int $timeout = 5;

    /**
     * CurlSender constructor.
     *
     * @param CurlClientFactory $curlClientFactory
     * @param string            $host
     */
    public function __construct(private readonly CurlClientFactory $curlClientFactory, private readonly string $host)
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
            $client            = $this->curlClientFactory->create(['timeout' => $this->timeout]);
            $fields['created'] = DateTimeUtils::getUtcDateTime()->getTimestamp();

            $data = [
                'fields' => $fields,
                'tags'   => $tags,
            ];

            $request = new Request(
                CurlManager::METHOD_POST,
                sprintf(
                    '%s/metrics/%s',
                    $this->host,
                    $isProcessMetrics ? 'monolith' : 'connectors',
                ),
                ['Content-Type' => 'application/json'],
                Json::encode($data),
            );
            $res     = $client->send($request);

            return $res->getStatusCode() === 200;
        } catch (Throwable) {
            return FALSE;
        }
    }

}
