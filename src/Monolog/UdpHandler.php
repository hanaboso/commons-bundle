<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Monolog;

use Hanaboso\CommonsBundle\Transport\Udp\UDPSender;
use Hanaboso\Utils\Exception\DateTimeException;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * Class UdpHandler
 *
 * @package Hanaboso\CommonsBundle\Monolog
 */
final class UdpHandler extends AbstractProcessingHandler
{

    /**
     * UdpHandler constructor.
     *
     * @param UDPSender $UDPSender
     * @param string    $host
     * @param int       $level
     * @param bool      $bubble
     */
    public function __construct(
        private UDPSender $UDPSender,
        private string $host,
        $level = Logger::DEBUG,
        $bubble = TRUE,
    )
    {
        parent::__construct($level, $bubble);
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param mixed[] $record
     *
     * @return void
     * @throws DateTimeException
     */
    protected function write(array $record): void
    {
        $this->UDPSender->send($this->host, $record['formatted']);
    }

}
