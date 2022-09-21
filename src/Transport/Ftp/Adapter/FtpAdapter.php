<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Ftp\Adapter;

use FTP\Connection;
use Hanaboso\CommonsBundle\Transport\Ftp\Exception\FtpException;
use Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig;

/**
 * Class FtpAdapter
 *
 * @package Hanaboso\CommonsBundle\Transport\Ftp\Adapter
 */
final class FtpAdapter implements FtpAdapterInterface
{

    /**
     * @var Connection|bool
     */
    private $ftp;

    /**
     * @param FtpConfig $ftpConfig
     *
     * @throws FtpException
     */
    public function connect(FtpConfig $ftpConfig): void
    {
        if ($ftpConfig->isSsl()) {
            $this->ftp = @ftp_ssl_connect($ftpConfig->getHost(), $ftpConfig->getPort(), $ftpConfig->getTimeout());
        } else {
            $this->ftp = @ftp_connect($ftpConfig->getHost(), $ftpConfig->getPort(), $ftpConfig->getTimeout());
        }

        if (is_bool($this->ftp)) {
            throw new FtpException('Connection to Ftp server failed.', FtpException::CONNECTION_FAILED);
        }
    }

    /**
     * @throws FtpException
     */
    public function disconnect(): void
    {
        $res = @ftp_close($this->getResource());
        if ($res === FALSE) {
            throw new FtpException('Connection close failed.', FtpException::CONNECTION_CLOSE_FAILED);
        }
    }

    /**
     * @param FtpConfig $ftpConfig
     *
     * @throws FtpException
     */
    public function login(FtpConfig $ftpConfig): void
    {
        $res = @ftp_login($this->getResource(), $ftpConfig->getUsername(), $ftpConfig->getPassword());

        if ($res === FALSE) {
            throw new FtpException('Login failed.', FtpException::LOGIN_FAILED);
        }

        ftp_pasv($this->getResource(), TRUE);
    }

    /**
     * @param string $remoteFile
     * @param string $localFile
     *
     * @throws FtpException
     */
    public function uploadFile(string $remoteFile, string $localFile): void
    {
        $res = ftp_put($this->getResource(), $remoteFile, $localFile);

        if ($res === FALSE) {
            throw new FtpException('File upload failed.', FtpException::FILE_UPLOAD_FAILED);
        }
    }

    /**
     * @param string $remoteFile
     * @param string $localFile
     *
     * @throws FtpException
     */
    public function downloadFile(string $remoteFile, string $localFile): void
    {
        $res = ftp_get($this->getResource(), $localFile, $remoteFile);

        if ($res === FALSE) {
            throw new FtpException('File download failed.', FtpException::FILE_DOWNLOAD_FAILED);
        }
    }

    /**
     * @param string $dir
     *
     * @return mixed[]
     * @throws FtpException
     */
    public function listDir(string $dir): array
    {
        $list = @ftp_nlist($this->getResource(), $dir);

        if ($list === FALSE) {
            throw new FtpException('Failed to list files in directory.', FtpException::FILES_LISTING_FAILED);
        }

        return $list;
    }

    /**
     * @param string $dir
     *
     * @return bool
     * @throws FtpException
     */
    public function dirExists(string $dir): bool
    {
        $current = (string) ftp_pwd($this->getResource());
        if (@ftp_chdir($this->getResource(), $dir)) {
            ftp_chdir($this->getResource(), $current);

            return TRUE;
        }

        return FALSE;
    }

    /**
     * @param string $dir
     *
     * @return void
     * @throws FtpException
     */
    public function makeDir(string $dir): void
    {
        $res = @ftp_mkdir($this->getResource(), $dir);
        if ($res === FALSE) {
            throw new FtpException(
                sprintf('Unable to create directory %s', $dir),
                FtpException::UNABLE_TO_CREATE_DIR,
            );
        }
    }

    /**
     * @param string $dir
     *
     * @return void
     * @throws FtpException
     */
    public function makeDirRecursive(string $dir): void
    {
        $current = (string) @ftp_pwd($this->getResource());
        $parts   = explode('/', trim($dir, '/'));

        foreach ($parts as $part) {
            if (!@ftp_chdir($this->getResource(), $part) && !$this->isFile($part)) {
                $this->makeDir($part);
                ftp_chdir($this->getResource(), $part);
            }
        }

        ftp_chdir($this->getResource(), $current);
    }

    /**
     * @param string $file
     *
     * @throws FtpException
     */
    public function remove(string $file): void
    {
        if (!@ftp_delete($this->getResource(), $file)) {
            throw new FtpException(
                sprintf('Unable to delete file or folder "%s"', $file),
            );
        }
    }

    /**
     * @param string $file
     *
     * @return bool
     * @throws FtpException
     */
    public function isFile(string $file): bool
    {
        return @ftp_size($this->getResource(), $file) !== -1;
    }

    /**************************************** HELPERS ****************************************/

    /**
     * @return mixed
     * @throws FtpException
     */
    private function getResource(): mixed
    {
        if (!is_bool($this->ftp)) {
            return $this->ftp;
        }

        throw new FtpException('Connection to Ftp server not established.', FtpException::CONNECTION_NOT_ESTABLISHED);
    }

}
