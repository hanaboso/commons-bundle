<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Udp;

use Exception;
use Hanaboso\CommonsBundle\Metrics\Exception\SystemMetricException;
use Hanaboso\Utils\Date\DateTimeUtils;
use Hanaboso\Utils\Exception\DateTimeException;
use Hanaboso\Utils\String\LoggerFormater;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Socket;

/**
 * Class UDPSender
 *
 * @package Hanaboso\CommonsBundle\Transport\Udp
 */
final class UDPSender implements LoggerAwareInterface
{

    private const APCU_IP      = 'metrics_collector_ip:';
    private const APCU_REFRESH = 'metrics_collector_refresh:';

    private const REFRESH_INTERVAL = 60;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var string[]
     */
    private array $ips = [];

    /**
     * @var int
     */
    private int $lastIPRefresh;

    /**
     * @var Socket|null
     */
    private $socket = NULL;

    /**
     * UDPSender constructor.
     */
    public function __construct()
    {
        $this->logger        = new NullLogger();
        $this->lastIPRefresh = 0;

        // limit the ip addr hostname resolution
        putenv('RES_OPTIONS=retrans:1 retry:1 timeout:1 attempts:1');
    }

    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return UDPSender
     */
    public function setLogger(LoggerInterface $logger): UDPSender
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param string $host
     * @param string $message
     *
     * @return bool
     * @throws DateTimeException
     */
    public function send(string $host, string $message): bool
    {
        $parsed = parse_url($host) ?: [];
        $ip     = $this->refreshIp($parsed['host'] ?? '');
        $socket = $this->getSocket();
        if ($socket === NULL) {
            return FALSE;
        }

        try {
            if ($ip === '') {
                throw new SystemMetricException(
                    sprintf('Could not sent udp packet. IP address for "%s" not resolved', $parsed['host'] ?? '')
                );
            }

            $sent = @socket_sendto($socket, $message, strlen($message), 0, $ip, intval($parsed['port'] ?? 80));

            if ($sent === FALSE) {
                throw new SystemMetricException(
                    sprintf('Unable to send udp packet. Err: %s', socket_strerror(socket_last_error()))
                );
            }

            return TRUE;
        } catch (Exception $e) {
            $this->logger->error(
                sprintf('Udp sender err: %s', $e->getMessage()),
                LoggerFormater::getContextForLogger($e)
            );

            return FALSE;
        }
    }

    /**
     * Returns the ip addr for the hostname
     * Does the periodical checks
     *
     * @param string $host
     *
     * @return string
     * @throws DateTimeException
     */
    public function refreshIp(string $host): string
    {
        // we want to refresh it only in predefined time periods
        if (DateTimeUtils::getUtcDateTime()->getTimestamp() <= $this->lastIPRefresh + self::REFRESH_INTERVAL &&
            isset($this->ips[$host])) {
            return $this->ips[$host];
        }

        $this->ips[$host]    = $this->getIp($host);
        $this->lastIPRefresh = DateTimeUtils::getUtcDateTime()->getTimestamp();

        apcu_delete(sprintf('%s%s', self::APCU_IP, $host));
        apcu_delete(sprintf('%s%s', self::APCU_REFRESH, $host));

        apcu_store(sprintf('%s%s', self::APCU_IP, $host), $this->ips[$host]);
        apcu_store(sprintf('%s%s', self::APCU_REFRESH, $host), $this->lastIPRefresh);

        return $this->ips[$host];
    }

    /**
     * Returns socket resource or null if socket cannot be created
     *
     * @return Socket|null
     */
    private function getSocket(): ?Socket
    {
        if ($this->socket && socket_last_error($this->socket) != 0) {
            $this->socket = NULL;
        }

        if (!$this->socket) {
            $socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            if ($socket === FALSE) {
                $this->logger->error(sprintf('Cannot create udp socket: %s', socket_strerror(socket_last_error())));
                $socket = NULL;
            }

            $this->socket = $socket;
        }

        return $this->socket;
    }

    /**
     * Returns the ip for the given hostname or returns empty string
     *
     * @param string $host
     *
     * @return string
     */
    private function getIp(string $host): string
    {
        $ip = gethostbyname($host);
        if ($ip !== $host) {
            return $ip;
        }

        return '';
    }

}
