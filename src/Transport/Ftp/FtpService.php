<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Ftp;

use Hanaboso\CommonsBundle\Transport\Ftp\Adapter\FtpAdapterInterface;
use Hanaboso\CommonsBundle\Transport\Ftp\Exception\FtpException;
use Hanaboso\Utils\File\File;
use Hanaboso\Utils\String\LoggerFormater;
use Hanaboso\Utils\Traits\LoggerTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\NullLogger;
use SplFileInfo;

/**
 * Class FtpService
 *
 * @package Hanaboso\CommonsBundle\Transport\Ftp
 */
final class FtpService implements FtpServiceInterface, LoggerAwareInterface
{

    use LoggerTrait;

    /**
     * FtpService constructor.
     *
     * @param FtpAdapterInterface $adapter
     * @param FtpConfig           $ftpConfig
     */
    public function __construct(protected FtpAdapterInterface $adapter, private FtpConfig $ftpConfig)
    {
        $this->logger = new NullLogger();
    }

    /**
     * @return FtpAdapterInterface
     */
    public function getAdapter(): FtpAdapterInterface
    {
        return $this->adapter;
    }

    /**
     * @param string $remoteFile
     * @param string $content
     *
     * @return bool
     * @throws FtpException
     */
    public function uploadFile(string $remoteFile, string $content): bool
    {
        $this->connect();
        $this->login();

        if (!$this->adapter->dirExists(dirname($remoteFile))) {
            $this->adapter->makeDirRecursive(dirname($remoteFile));
        }

        $filename = (string) tempnam(sys_get_temp_dir(), 'tmp');
        File::putContent($filename, $content);

        try {
            $this->adapter->uploadFile($remoteFile, $filename);
            $this->logger->debug(sprintf('File %s successfully uploaded.', $remoteFile));
        } catch (FtpException $e) {
            $this->logger->error($e->getMessage(), LoggerFormater::getContextForLogger($e));

            throw $e;
        } finally {
            unlink($filename);
            $this->disconnect();
        }

        return TRUE;
    }

    /**
     * @param string $remoteFile
     *
     * @return SplFileInfo
     * @throws FtpException
     */
    public function downloadFile(string $remoteFile): SplFileInfo
    {
        $this->connect();
        $this->login();

        $filename  = basename($remoteFile);
        $localFile = sprintf('%s%s%s', sys_get_temp_dir(), DIRECTORY_SEPARATOR, $filename);

        try {
            $this->adapter->downloadFile($remoteFile, $localFile);
            $this->logger->debug(sprintf('File %s successfully downloaded to %s.', $remoteFile, $localFile));
        } catch (FtpException $e) {
            $this->logger->error($e->getMessage(), LoggerFormater::getContextForLogger($e));

            throw $e;
        } finally {
            $this->disconnect();
        }

        return new SplFileInfo($localFile);
    }

    /**
     * @param string $dir
     *
     * @return mixed[]
     * @throws FtpException
     */
    public function downloadFiles(string $dir): array
    {
        $this->connect();
        $this->login();

        $downloaded = [];

        try {
            $list = $this->adapter->listDir($dir);
            $this->logger->debug(sprintf('Downloading files from %s directory', $dir));

            foreach ($list as $file) {
                $downloaded[] = $this->downloadFile(sprintf('%s/%s', trim($dir, '/'), $file));
            }

            $this->logger->debug('Downloading files finished successfully.');
        } catch (FtpException $e) {
            $this->logger->error($e->getMessage(), LoggerFormater::getContextForLogger($e));

            throw $e;
        } finally {
            $this->disconnect();
        }

        return $downloaded;
    }

    /**************************************** HELPERS ****************************************/

    /**
     * @throws FtpException
     */
    private function connect(): void
    {
        try {
            $this->adapter->connect($this->ftpConfig);
        } catch (FtpException $e) {
            $this->logger->error($e->getMessage(), LoggerFormater::getContextForLogger($e));

            throw $e;
        }
    }

    /**
     * @throws FtpException
     */
    private function disconnect(): void
    {
        try {
            $this->adapter->disconnect();
        } catch (FtpException $e) {
            $this->logger->error($e->getMessage(), LoggerFormater::getContextForLogger($e));

            throw $e;
        }
    }

    /**
     * @throws FtpException
     */
    private function login(): void
    {
        try {
            $this->adapter->login($this->ftpConfig);
        } catch (FtpException $e) {
            $this->logger->error($e->getMessage(), LoggerFormater::getContextForLogger($e));

            throw $e;
        }
    }

}
