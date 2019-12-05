<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\AsyncCurl;

use Clue\React\Buzz\Browser;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
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
    private LoggerInterface $logger;

    /**
     * @var MetricsSenderLoader|null
     */
    private ?MetricsSenderLoader $metricsLoader;

    /**
     * CurlSenderFactory constructor.
     */
    public function __construct()
    {
        $this->logger        = new NullLogger();
        $this->metricsLoader = NULL;
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
     * @param MetricsSenderLoader $metricsLoader
     *
     * @return CurlSenderFactory
     */
    public function setMetricsSender(MetricsSenderLoader $metricsLoader): CurlSenderFactory
    {
        $this->metricsLoader = $metricsLoader;

        return $this;
    }

    /**
     * @param LoopInterface $loop
     * @param mixed[]       $secret
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

        if ($this->metricsLoader) {
            $curlSender->setMetricsSender($this->metricsLoader);
        }

        return $curlSender;
    }

}
