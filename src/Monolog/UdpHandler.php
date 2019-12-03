<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Monolog;

use Hanaboso\CommonsBundle\Exception\DateTimeException;
use Hanaboso\CommonsBundle\Metrics\Impl\UDPSender;
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
     * UdpHandler constructor.
     *
     * @param UDPSender $UDPSender
     * @param int       $level
     * @param bool      $bubble
     */
    public function __construct(UDPSender $UDPSender, $level = Logger::DEBUG, $bubble = TRUE)
    {
        parent::__construct($level, $bubble);

        $this->UDPSender = $UDPSender;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param array $record
     *
     * @return void
     * @throws DateTimeException
     */
    protected function write(array $record): void
    {
        $this->UDPSender->send($record['formatted']);
    }

}
