<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Ftp;

use Hanaboso\CommonsBundle\Transport\Ftp\Adapter\FtpAdapter;
use Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter;
use Hanaboso\CommonsBundle\Transport\Ftp\Exception\FtpException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FtpServiceFactory
 *
 * @package Hanaboso\CommonsBundle\Transport\Ftp
 */
final class FtpServiceFactory implements LoggerAwareInterface
{

    public const ADAPTER_FTP  = 'ftp';
    public const ADAPTER_SFTP = 'sftp';

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * FtpServiceFactory constructor.
     *
     * @param FtpAdapter         $ftpAdapter
     * @param SftpAdapter        $sftpAdapter
     * @param ContainerInterface $container
     */
    public function __construct(
        private FtpAdapter $ftpAdapter,
        private SftpAdapter $sftpAdapter,
        private ContainerInterface $container,
    )
    {
        $this->logger = new NullLogger();
    }

    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return FtpServiceFactory
     */
    public function setLogger(LoggerInterface $logger): FtpServiceFactory
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return FtpService
     * @throws FtpException
     */
    public function getFtpService(string $type): FtpService
    {
        $service = match ($type) {
            self::ADAPTER_FTP => new FtpService($this->ftpAdapter, $this->prepareConfig(self::ADAPTER_FTP)),
            self::ADAPTER_SFTP => new FtpService($this->sftpAdapter, $this->prepareConfig(self::ADAPTER_SFTP)),
            default => throw new FtpException(
                sprintf('Unknown ftp adapter type "%s"', $type),
                FtpException::UNKNOWN_ADAPTER_TYPE,
            ),
        };

        $service->setLogger($this->logger);

        return $service;
    }

    /**
     * @param string $prefix
     *
     * @return FtpConfig
     */
    private function prepareConfig(string $prefix): FtpConfig
    {
        /** @var string $host */
        $host = $this->container->getParameter(sprintf('%s.host', $prefix));

        /** @var int $port */
        $port = $this->container->getParameter(sprintf('%s.port', $prefix));

        /** @var int $timeout */
        $timeout = $this->container->getParameter(sprintf('%s.timeout', $prefix));

        /** @var string $user */
        $user = $this->container->getParameter(sprintf('%s.user', $prefix));

        /** @var string $password */
        $password = $this->container->getParameter(sprintf('%s.password', $prefix));

        /** @var bool $ssl */
        $ssl = $prefix == self::ADAPTER_FTP ? $this->container->getParameter(sprintf('%s.ssl', $prefix)) : FALSE;

        return new FtpConfig($host, $ssl, $port, $timeout, $user, $password);
    }

}
