<?php declare(strict_types=1);

namespace Tests\Unit\Transport\Ftp;

use Exception;
use Hanaboso\CommonsBundle\Transport\Ftp\Adapter\FtpAdapter;
use Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig;
use Hanaboso\CommonsBundle\Transport\Ftp\FtpService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

/**
 * Class FtpServiceTest
 *
 * @package Tests\Unit\Transport\Ftp
 */
final class FtpServiceTest extends TestCase
{

    /**
     * @covers FtpService::uploadFile()
     * @throws Exception
     */
    public function testUploadFile(): void
    {
        /** @var MockObject|FtpAdapter $adapter */
        $adapter = self::createPartialMock(
            FtpAdapter::class,
            ['connect', 'login', 'disconnect', 'dirExists', 'makeDirRecursive', 'uploadFile']
        );
        $adapter->expects(self::any())->method('connect')->willReturn(TRUE);
        $adapter->expects(self::any())->method('login')->willReturn(TRUE);
        $adapter->expects(self::any())->method('disconnect')->willReturn(TRUE);
        $adapter->expects(self::any())->method('dirExists')->willReturn(FALSE);
        $adapter->expects(self::any())->method('makeDirRecursive')->willReturn(TRUE);
        $adapter->expects(self::any())->method('uploadFile')->willReturn(TRUE);

        $service = new FtpService($adapter, $this->getFtpConfig());
        $result  = $service->uploadFile('abc', 'def');

        self::assertTrue($result);
    }

    /**
     * @covers FtpService::downloadFile()
     * @throws Exception
     */
    public function testDownloadFile(): void
    {
        /** @var MockObject|FtpAdapter $adapter */
        $adapter = self::createPartialMock(
            FtpAdapter::class,
            ['connect', 'login', 'disconnect', 'downloadFile']
        );
        $adapter->expects(self::any())->method('connect')->willReturn(TRUE);
        $adapter->expects(self::any())->method('login')->willReturn(TRUE);
        $adapter->expects(self::any())->method('disconnect')->willReturn(TRUE);
        $adapter->expects(self::any())->method('downloadFile')->willReturn(TRUE);

        $service = new FtpService($adapter, $this->getFtpConfig());
        $result  = $service->downloadFile('abc');

        self::assertInstanceOf(SplFileInfo::class, $result);
        self::assertEquals('abc', $result->getBasename());
    }

    /**
     * @covers FtpService::downloadFiles()
     * @throws Exception
     */
    public function testDownloadFiles(): void
    {
        /** @var MockObject|FtpAdapter $adapter */
        $adapter = self::createPartialMock(
            FtpAdapter::class,
            ['connect', 'login', 'disconnect', 'listDir', 'downloadFile']
        );
        $adapter->expects(self::any())->method('connect')->willReturn(TRUE);
        $adapter->expects(self::any())->method('login')->willReturn(TRUE);
        $adapter->expects(self::any())->method('disconnect')->willReturn(TRUE);
        $adapter->expects(self::any())->method('listDir')->willReturn(['abc', 'def']);
        $adapter->expects(self::any())->method('downloadFile')->willReturn(TRUE);

        $service = new FtpService($adapter, $this->getFtpConfig());
        /** @var SplFileInfo[] $result */
        $result = $service->downloadFiles('abc');

        self::assertCount(2, $result);
        self::assertInstanceOf(SplFileInfo::class, $result[0]);
        self::assertInstanceOf(SplFileInfo::class, $result[1]);
        self::assertEquals('abc', $result[0]->getBasename());
        self::assertEquals('def', $result[1]->getBasename());
    }

    /**
     * @return FtpConfig
     */
    private function getFtpConfig(): FtpConfig
    {
        return new FtpConfig('', FALSE, 21, 15, '', '');
    }

}
