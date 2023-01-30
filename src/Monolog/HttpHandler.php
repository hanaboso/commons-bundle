<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Monolog;

use GuzzleHttp\Psr7\Request;
use Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\Utils\String\Json;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Throwable;

/**
 * Class HttpHandler
 *
 * @package Hanaboso\CommonsBundle\Monolog
 */
final class HttpHandler extends AbstractProcessingHandler
{

    /**
     * @var int
     */
    private int $timeout = 5;

    /**
     * HttpHandler constructor.
     *
     * @param CurlClientFactory $curlClientFactory
     * @param string            $host
     * @param Level             $level
     * @param bool              $bubble
     */
    public function __construct(
        private readonly CurlClientFactory $curlClientFactory,
        private readonly string $host,
        Level $level = Level::Debug,
        bool $bubble = TRUE,
    )
    {
        parent::__construct($level, $bubble);
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param LogRecord $record
     *
     * @return void
     */
    protected function write(LogRecord $record): void
    {
        try {
            if (isset($record['context']['is_for_ui']) && $record['context']['is_for_ui']) {
                $client  = $this->curlClientFactory->create(['timeout' => $this->timeout]);
                $request = new Request(
                    CurlManager::METHOD_POST,
                    sprintf('%s/logger/logs', $this->host),
                    ['Content-Type' => 'application/json'],
                    Json::encode($record),
                );
                $client->send($request);
            }
        } catch (Throwable) {}
    }

}
