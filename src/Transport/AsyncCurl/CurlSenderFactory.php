<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\AsyncCurl;

use Clue\React\Buzz\Browser;
use Hanaboso\CommonsBundle\Metrics\InfluxDbSender;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use React\EventLoop\LoopInterface;
use React\Socket\Connector;
use React\Socket\SecureConnector;

/**
 * Class CurlSenderFactory
 *
 * @package Hanaboso\CommonsBundle\Transport\AsyncCurl
 */
class CurlSenderFactory implements LoggerAwareInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var InfluxDbSender|null
     */
    private $influxSender;

    /**
     * CurlSenderFactory constructor.
     */
    public function __construct()
    {
        $this->logger       = new NullLogger();
        $this->influxSender = NULL;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return CurlSenderFactory
     */
    public function setLogger(LoggerInterface $logger): CurlSenderFactory
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param InfluxDbSender $influxSender
     *
     * @return CurlSenderFactory
     */
    public function setInfluxSender(InfluxDbSender $influxSender): CurlSenderFactory
    {
        $this->influxSender = $influxSender;

        return $this;
    }

    /**
     * @param LoopInterface $loop
     * @param array         $secret
     *
     * @return CurlSender
     */
    public function create(LoopInterface $loop, array $secret = []): CurlSender
    {
        $browser = new Browser($loop);

        if (isset($secret['ca']) && isset($secret['cert'])) {
            $context = [
                'verify_peer' => TRUE,
                'cafile'      => $secret['ca'],
                'local_cert'  => $secret['cert'],
            ];
            $browser = new Browser($loop, new SecureConnector(new Connector($loop), $loop, $context));
        }

        $curlSender = new CurlSender($browser);
        $curlSender->setLogger($this->logger);

        if ($this->influxSender) {
            $curlSender->setInfluxSender($this->influxSender);
        }

        return $curlSender;
    }

}
