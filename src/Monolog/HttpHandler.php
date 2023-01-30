<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Monolog;

use Hanaboso\CommonsBundle\WorkerApi\Client;
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
     * HttpHandler constructor.
     *
     * @param Client $client
     * @param Level  $level
     * @param bool   $bubble
     */
    public function __construct(private readonly Client $client, Level $level = Level::Debug, bool $bubble = TRUE,)
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
                $this->client->send('/logger/logs', $record);
            }
        } catch (Throwable) {}
    }

}
