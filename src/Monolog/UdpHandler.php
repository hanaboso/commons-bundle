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
class UdpHandler extends AbstractProcessingHandler
{

    /**
     * @var UDPSender
     */
    private UDPSender $UDPSender;

    /**
     * @var string
     */
    private string $host;

    /**
     * UdpHandler constructor.
     *
     * @param UDPSender $UDPSender
     * @param string    $host
     * @param int       $level
     * @param bool      $bubble
     */
    public function __construct(UDPSender $UDPSender, string $host, $level = Logger::DEBUG, $bubble = TRUE)
    {
        parent::__construct($level, $bubble);

        $this->UDPSender = $UDPSender;
        $this->host      = $host;
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
