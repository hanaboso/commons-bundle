<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Ftp;

/**
 * Class FtpConfig
 *
 * @package Hanaboso\CommonsBundle\Transport\Ftp
 */
final class FtpConfig
{

    /**
     * FtpConfig constructor.
     *
     * @param string $host
     * @param bool   $ssl
     * @param int    $port
     * @param int    $timeout
     * @param string $username
     * @param string $password
     */
    public function __construct(
        private string $host,
        private bool $ssl,
        private int $port,
        private int $timeout,
        private string $username,
        private string $password
    )
    {
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return bool
     */
    public function isSsl(): bool
    {
        return $this->ssl;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

}
