<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Ftp;

use Exception;
use Hanaboso\CommonsBundle\Transport\Ftp\Adapter\FtpAdapter;
use Hanaboso\CommonsBundle\Transport\Ftp\Exception\FtpException;
use Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig;
use Hanaboso\CommonsBundle\Transport\Ftp\FtpService;
use Monolog\Logger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

/**
 * Class FtpServiceTest
 *
 * @package CommonsBundleTests\Unit\Transport\Ftp
 */
#[CoversClass(FtpService::class)]
final class FtpServiceTest extends TestCase
{

    /**
     * @throws Exception
     */
    public function testUploadFile(): void
    {
        $adapter = self::createPartialMock(
            FtpAdapter::class,
            ['connect', 'login', 'disconnect', 'dirExists', 'makeDirRecursive', 'uploadFile'],
        );
        $adapter->expects(self::any())->method('connect');
        $adapter->expects(self::any())->method('login');
        $adapter->expects(self::any())->method('disconnect');
        $adapter->expects(self::any())->method('dirExists')->willReturn(FALSE);
        $adapter->expects(self::any())->method('makeDirRecursive');
        $adapter->expects(self::any())->method('uploadFile');

        $service = new FtpService($adapter, $this->getFtpConfig());
        $result  = $service->uploadFile('abc', 'def');

        self::assertTrue($result);
    }

    /**
     * @throws Exception
     */
    public function testDownloadFile(): void
    {
        $adapter = self::createPartialMock(
            FtpAdapter::class,
            ['connect', 'login', 'disconnect', 'downloadFile'],
        );
        $adapter->expects(self::any())->method('connect');
        $adapter->expects(self::any())->method('login');
        $adapter->expects(self::any())->method('disconnect');
        $adapter->expects(self::any())->method('downloadFile');

        $service = new FtpService($adapter, $this->getFtpConfig());
        $result  = $service->downloadFile('abc');

        self::assertEquals('abc', $result->getBasename());
    }

    /**
     * @throws Exception
     */
    public function testDownloadFiles(): void
    {
        $adapter = self::createPartialMock(
            FtpAdapter::class,
            ['connect', 'login', 'disconnect', 'listDir', 'downloadFile'],
        );
        $adapter->expects(self::any())->method('connect');
        $adapter->expects(self::any())->method('login');
        $adapter->expects(self::any())->method('disconnect');
        $adapter->expects(self::any())->method('listDir')->willReturn(['abc', 'def']);
        $adapter->expects(self::any())->method('downloadFile');

        $service = new FtpService($adapter, $this->getFtpConfig());
        /** @var SplFileInfo[] $result */
        $result = $service->downloadFiles('abc');

        self::assertCount(2, $result);
        self::assertEquals('abc', $result[0]->getBasename());
        self::assertEquals('def', $result[1]->getBasename());
    }

    /**
     * @return void
     * @throws FtpException
     */
    public function testGetAdapter(): void
    {
        $adapter = self::createMock(FtpAdapter::class);

        $service = new FtpService($adapter, $this->getFtpConfig());
        $service->setLogger(new Logger('name'));
        $result = $service->getAdapter();

        self::assertInstanceOf(FtpAdapter::class, $result);
    }

    /**
     * @throws FtpException
     */
    public function testUploadFileErr(): void
    {
        $adapter = self::createPartialMock(
            FtpAdapter::class,
            ['uploadFile', 'connect', 'login', 'dirExists', 'makeDirRecursive', 'disconnect'],
        );
        $adapter->expects(self::any())->method('connect');
        $adapter->expects(self::any())->method('login');
        $adapter->expects(self::any())->method('disconnect');
        $adapter->expects(self::any())->method('uploadFile')->willThrowException(new FtpException());
        $adapter->expects(self::any())->method('dirExists')->willReturn(FALSE);
        $adapter->expects(self::any())->method('makeDirRecursive');

        $service = new FtpService($adapter, $this->getFtpConfig());

        self::expectException(FtpException::class);
        $service->uploadFile('file', 'content');
    }

    /**
     * @throws FtpException
     */
    public function testDownloadFileErr(): void
    {
        $adapter = self::createPartialMock(
            FtpAdapter::class,
            ['downloadFile', 'connect', 'login', 'disconnect'],
        );
        $adapter->expects(self::any())->method('connect');
        $adapter->expects(self::any())->method('login');
        $adapter->expects(self::any())->method('disconnect');
        $adapter->expects(self::any())->method('downloadFile')->willThrowException(new FtpException());

        $service = new FtpService($adapter, $this->getFtpConfig());

        self::expectException(FtpException::class);
        $service->downloadFile('file');
    }

    /**
     * @throws FtpException
     */
    public function testDownloadFilesErr(): void
    {
        $adapter = self::createPartialMock(
            FtpAdapter::class,
            ['downloadFile', 'connect', 'login', 'listDir', 'disconnect'],
        );
        $adapter->expects(self::any())->method('connect');
        $adapter->expects(self::any())->method('login');
        $adapter->expects(self::any())->method('disconnect');
        $adapter->expects(self::any())->method('downloadFile')->willThrowException(new FtpException());
        $adapter->expects(self::any())->method('listDir')->willReturn(['abc', 'def']);

        $service = new FtpService($adapter, $this->getFtpConfig());

        self::expectException(FtpException::class);
        $service->downloadFiles('file');
    }

    /**
     * @throws FtpException
     */
    public function testConnectErr(): void
    {
        $adapter = self::createPartialMock(FtpAdapter::class, ['connect']);
        $adapter->expects(self::any())->method('connect')->willThrowException(new FtpException());

        $service = new FtpService($adapter, $this->getFtpConfig());

        self::expectException(FtpException::class);
        $service->uploadFile('file', 'content');
    }

    /**
     * @throws FtpException
     */
    public function testDisconnectErr(): void
    {
        $adapter = self::createPartialMock(FtpAdapter::class, ['connect', 'login', 'downloadFile', 'disconnect']);
        $adapter->expects(self::any())->method('connect');
        $adapter->expects(self::any())->method('login');
        $adapter->expects(self::any())->method('downloadFile');
        $adapter->expects(self::any())->method('disconnect')->willThrowException(new FtpException());

        $service = new FtpService($adapter, $this->getFtpConfig());

        self::expectException(FtpException::class);
        $service->downloadFile('file');
    }

    /**
     * @throws FtpException
     */
    public function testLogin(): void
    {
        $adapter = self::createPartialMock(FtpAdapter::class, ['connect', 'login']);
        $adapter->expects(self::any())->method('connect');
        $adapter->expects(self::any())->method('login')->willThrowException(new FtpException());

        $service = new FtpService($adapter, $this->getFtpConfig());

        self::expectException(FtpException::class);
        $service->downloadFile('file');
    }

    /**
     * @return FtpConfig
     */
    private function getFtpConfig(): FtpConfig
    {
        return new FtpConfig('', FALSE, 21, 15, '', '');
    }

}
