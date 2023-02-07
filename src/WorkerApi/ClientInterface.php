<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\WorkerApi;

use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Monolog\LogRecord;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface ClientInterface
 *
 * @package Hanaboso\CommonsBundle\WorkerApi
 */
interface ClientInterface
{

    /**
     * @param string                 $uri
     * @param mixed[]|LogRecord|null $data
     * @param string                 $method
     *
     * @return ResponseInterface
     */
    public function send(
        string $uri,
        array | LogRecord|null $data = NULL,
        string $method = CurlManager::METHOD_POST,
    ): ResponseInterface;

}
