<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\WorkerApi;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\Utils\String\Json;
use Monolog\LogRecord;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client
 *
 * @package Hanaboso\CommonsBundle\WorkerApi
 */
final class Client implements ClientInterface
{

    /**
     * @var int
     */
    private int $timeout = 5;

    /**
     * @var GuzzleClient $client
     */
    private GuzzleClient $client;

    /**
     * Client constructor.
     *
     * @param CurlClientFactory $curlClientFactory
     * @param string            $host
     * @param string            $apiKey
     */
    public function __construct(
        CurlClientFactory $curlClientFactory,
        private readonly string $host,
        private readonly string $apiKey,
    )
    {
        $this->client = $curlClientFactory->create(['timeout' => $this->timeout]);
    }

    /**
     * @param string                 $uri
     * @param mixed[]|LogRecord|null $data
     * @param string                 $method
     *
     * @return ResponseInterface|null
     * @throws GuzzleException
     */
    public function send(
        string $uri,
        array | LogRecord|null $data = NULL,
        string $method = CurlManager::METHOD_POST,
    ): ResponseInterface|null
    {
        $request = new Request(
            $method,
            sprintf('%s%s', $this->host, $uri),
            [
                'Content-Type' => 'application/json',
                'orchesty-api-key' => $this->apiKey,
            ],
            Json::encode($data),
        );

        return $this->client->send($request);
    }

}
