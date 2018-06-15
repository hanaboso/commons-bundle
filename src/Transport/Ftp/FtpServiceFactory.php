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
class FtpServiceFactory implements LoggerAwareInterface
{

    public const ADAPTER_FTP  = 'ftp';
    public const ADAPTER_SFTP = 'sftp';

    /**
     * @var FtpAdapter
     */
    private $ftpAdapter;

    /**
     * @var SftpAdapter
     */
    private $sftpAdapter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * FtpServiceFactory constructor.
     *
     * @param FtpAdapter         $ftpAdapter
     * @param SftpAdapter        $sftpAdapter
     * @param ContainerInterface $container
     */
    public function __construct(
        FtpAdapter $ftpAdapter,
        SftpAdapter $sftpAdapter,
        ContainerInterface $container
    )
    {
        $this->ftpAdapter  = $ftpAdapter;
        $this->sftpAdapter = $sftpAdapter;
        $this->container   = $container;
        $this->logger      = new NullLogger();
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
        switch ($type) {
            case self::ADAPTER_FTP:
                $service = new FtpService($this->ftpAdapter, $this->prepareConfig(self::ADAPTER_FTP));
                break;
            case self::ADAPTER_SFTP:
                $service = new FtpService($this->sftpAdapter, $this->prepareConfig(self::ADAPTER_SFTP));
                break;
            default:
                throw new FtpException(
                    sprintf('Unknown ftp adapter type "%s"', $type),
                    FtpException::UNKNOWN_ADAPTER_TYPE
                );
        }

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
        return new FtpConfig(
            $this->container->getParameter($prefix . '.host'),
            $prefix == self::ADAPTER_FTP ? $this->container->getParameter($prefix . '.ssl') : FALSE,
            $this->container->getParameter($prefix . '.port'),
            $this->container->getParameter($prefix . '.timeout'),
            $this->container->getParameter($prefix . '.user'),
            $this->container->getParameter($prefix . '.password')
        );
    }

}