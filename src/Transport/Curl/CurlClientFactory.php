<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Curl;

use GuzzleHttp\Client;

/**
 * Class CurlClientFactory
 *
 * @package Hanaboso\CommonsBundle\Transport\Curl
 */
class CurlClientFactory
{

    /**
     * @param mixed[] $config
     *
     * @return Client
     */
    public function create(array $config = []): Client
    {
        return new Client($config);
    }

}
