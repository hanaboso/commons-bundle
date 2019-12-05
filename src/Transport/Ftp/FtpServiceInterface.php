<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Ftp;

use SplFileInfo;

/**
 * Interface FtpServiceInterface
 *
 * @package Hanaboso\CommonsBundle\Transport\Ftp
 */
interface FtpServiceInterface
{

    public const HOST    = 'host';
    public const SSL     = 'ssl';
    public const PORT    = 'port';
    public const TIMEOUT = 'timeout';

    /**
     * @param string $remoteFile
     * @param string $content
     *
     * @return bool
     */
    public function uploadFile(string $remoteFile, string $content): bool;

    /**
     * @param string $remoteFile
     *
     * @return SplFileInfo
     */
    public function downloadFile(string $remoteFile): SplFileInfo;

    /**
     * @param string $dir
     *
     * @return mixed[]
     */
    public function downloadFiles(string $dir): array;

}
