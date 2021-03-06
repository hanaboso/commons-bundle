<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Ftp\Adapter;

use Hanaboso\CommonsBundle\Transport\Ftp\Exception\FtpException;
use Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig;
use phpseclib3\Net\SFTP;

/**
 * Class SftpAdapter
 *
 * @package Hanaboso\CommonsBundle\Transport\Ftp\Adapter
 */
final class SftpAdapter implements FtpAdapterInterface
{

    /**
     * @var SFTP|null
     */
    private ?SFTP $sftp;

    /**
     * @var int
     */
    private int $mode = 1;

    /**
     * @param FtpConfig $ftpConfig
     */
    public function connect(FtpConfig $ftpConfig): void
    {
        $this->sftp = new SFTP($ftpConfig->getHost(), $ftpConfig->getPort(), $ftpConfig->getTimeout());
        $this->mode = SFTP::SOURCE_LOCAL_FILE;
    }

    /**
     * @param FtpConfig $ftpConfig
     *
     * @throws FtpException
     */
    public function login(FtpConfig $ftpConfig): void
    {
        if (!$this->sftp || !$this->sftp->login($ftpConfig->getUsername(), [$ftpConfig->getPassword()])) {
            throw new FtpException('Login failed.', FtpException::LOGIN_FAILED);
        }
    }

    /**
     * @throws FtpException
     */
    public function disconnect(): void
    {
        $this->getResource();
        $this->sftp = NULL;
    }

    /**
     * @param string $remoteFile
     * @param string $localFile
     *
     * @throws FtpException
     */
    public function uploadFile(string $remoteFile, string $localFile): void
    {
        if (!$this->getResource()->put($remoteFile, $localFile, $this->mode)) {
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
        if ($this->getResource()->get($remoteFile, $localFile) === FALSE) {
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
        $list = $this->getResource()->nlist($dir);

        if (!$list) {
            throw new FtpException('Failed to list files in directory.', FtpException::FILES_LISTING_FAILED);
        }

        $files = [];
        foreach ($list as $item) {
            if (!in_array($item, ['.', '..'], TRUE)) {
                $files[] = $item;
            }
        }

        return $files;
    }

    /**
     * @param string $dir
     *
     * @return mixed[]
     * @throws FtpException
     */
    public function listDirAdvanced(string $dir): array
    {
        $list = $this->getResource()->rawlist($dir);

        if (!$list) {
            throw new FtpException('Failed to list files in directory.', FtpException::FILES_LISTING_FAILED);
        }

        $files = [];

        foreach ($list as $item) {
            if (!in_array($item['filename'], ['.', '..'], TRUE)) {
                $files[] = [
                    'name' => $item['filename'],
                    'path' => sprintf('%s/%s', $dir, $item['filename']),
                    'size' => $item['size'],
                    'time' => $item['mtime'],
                ];
            }
        }

        return $files;
    }

    /**
     * @param string $dir
     *
     * @return bool
     * @throws FtpException
     */
    public function dirExists(string $dir): bool
    {
        return $this->getResource()->is_dir($dir);
    }

    /**
     * @param string $dir
     *
     * @return void
     * @throws FtpException
     */
    public function makeDir(string $dir): void
    {
        $mkdir = $this->getResource()->mkdir($dir);

        if (!$mkdir) {
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
        $current = $this->getResource()->pwd();
        $parts   = explode('/', trim($dir, '/'));

        foreach ($parts as $part) {
            if (!$this->getResource()->chdir($part) && !$this->isFile($part)) {
                $this->makeDir($part);
                $this->getResource()->chdir($part);
            }
        }

        $this->getResource()->chdir($current);
    }

    /**
     * @param string $file
     *
     * @throws FtpException
     */
    public function remove(string $file): void
    {
        if (!$this->getResource()->delete($file, FALSE)) {
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
        return $this->getResource()->is_file($file);
    }

    /**
     * @param string $oldName
     * @param string $newName
     *
     * @return bool
     * @throws FtpException
     */
    public function rename(string $oldName, string $newName): bool
    {
        return $this->getResource()->rename($oldName, $newName);
    }

    /**
     * @param string $path
     *
     * @return bool
     * @throws FtpException
     */
    public function fileExists(string $path): bool
    {
        return $this->getResource()->file_exists($path);
    }

    /**************************************** HELPERS ****************************************/

    /**
     * @return SFTP
     * @throws FtpException
     */
    private function getResource(): SFTP
    {
        if ($this->sftp && $this->sftp->isConnected()) {
            return $this->sftp;
        }

        throw new FtpException('Connection to Ftp server not established.', FtpException::CONNECTION_NOT_ESTABLISHED);
    }

}
