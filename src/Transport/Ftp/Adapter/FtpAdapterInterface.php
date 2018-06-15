<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Ftp\Adapter;

use Hanaboso\CommonsBundle\Transport\Ftp\Exception\FtpException;
use Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig;

/**
 * Interface FtpAdapterInterface
 *
 * @package Hanaboso\CommonsBundle\Transport\Ftp\Adapter
 */
interface FtpAdapterInterface
{

    /**
     * @param FtpConfig $ftpConfig
     *
     * @throws FtpException
     */
    public function connect(FtpConfig $ftpConfig): void;

    /**
     * @throws FtpException
     */
    public function disconnect(): void;

    /**
     * @param FtpConfig $ftpConfig
     *
     * @throws FtpException
     */
    public function login(FtpConfig $ftpConfig): void;

    /**
     * @param string $remoteFile
     * @param string $localFile
     *
     * @throws FtpException
     */
    public function uploadFile(string $remoteFile, string $localFile): void;

    /**
     * @param string $remoteFile
     * @param string $localFile
     *
     * @throws FtpException
     */
    public function downloadFile(string $remoteFile, string $localFile): void;

    /**
     * @param string $dir
     *
     * @return array
     * @throws FtpException
     */
    public function listDir(string $dir): array;

    /**
     * @param string $dir
     *
     * @return bool
     */
    public function dirExists(string $dir): bool;

    /**
     * @param string $dir
     *
     * @return void
     * @throws FtpException
     */
    public function makeDir(string $dir): void;

    /**
     * @param string $dir
     *
     * @return void
     * @throws FtpException
     */
    public function makeDirRecursive($dir): void;

}