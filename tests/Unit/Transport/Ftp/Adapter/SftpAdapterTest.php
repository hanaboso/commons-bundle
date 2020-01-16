<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Ftp\Adapter;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter;
use Hanaboso\CommonsBundle\Transport\Ftp\Exception\FtpException;
use Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use phpmock\phpunit\PHPMock;
use phpseclib\Net\SFTP;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

/**
 * Class SftpAdapterTest
 *
 * @package CommonsBundleTests\Unit\Transport\Ftp\Adapter
 */
final class SftpAdapterTest extends KernelTestCaseAbstract
{

    use PHPMock;
    use PrivateTrait;

    /**
     * @var SftpAdapter
     */
    private SftpAdapter $adapter;

    /**
     * @var SFTP | MockObject
     */
    private $sftp;

    /**
     *
     */
    public function setUp(): void
    {
        $this->sftp    = self::createMock(SFTP::class);
        $this->adapter = new SftpAdapter();
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::connect
     *
     * @throws FtpException
     */
    public function testConnection(): void
    {
        $this->adapter->connect($this->config());

        self::assertTrue(TRUE);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::login
     *
     * @throws ReflectionException
     * @throws FtpException
     */
    public function testLogin(): void
    {
        $this->mockSftpFn(['login' => TRUE]);
        $this->adapter->login($this->config());

        self::assertTrue(TRUE);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::login
     * @throws FtpException
     * @throws ReflectionException
     */
    public function testLoginErr(): void
    {
        $this->mockSftpFn(['login' => FALSE]);

        self::expectException(FtpException::class);
        $this->adapter->login($this->config());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::disconnect
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::getResource
     *
     * @throws ReflectionException
     * @throws FtpException
     */
    public function testDisconnect(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE]);
        $this->adapter->disconnect();

        self::assertTrue(TRUE);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::uploadFile
     *
     * @throws ReflectionException
     * @throws FtpException
     */
    public function testUploadFile(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'put' => TRUE]);

        $this->adapter->uploadFile('file', 'path');
        self::assertTrue(TRUE);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::uploadFile
     *
     * @throws ReflectionException
     * @throws FtpException
     */
    public function testUploadFileErr(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'put' => FALSE]);

        self::expectException(FtpException::class);
        $this->adapter->uploadFile('file', 'path');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::downloadFile
     *
     * @throws ReflectionException
     * @throws FtpException
     */
    public function testDownloadFile(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'get' => TRUE]);

        $this->adapter->downloadFile('file', 'path');
        self::assertTrue(TRUE);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::downloadFile
     *
     * @throws ReflectionException
     * @throws FtpException
     */
    public function testDownloadFileErr(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'get' => FALSE]);

        self::expectException(FtpException::class);
        $this->adapter->downloadFile('file', 'path');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::listDir()
     *
     * @throws ReflectionException
     * @throws FtpException
     */
    public function testListDir(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'nlist' => ['el1', 'el2']]);

        self::assertEquals(
            [
                0 => 'el1',
                1 => 'el2',
            ],
            $this->adapter->listDir('dir')
        );
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::listDir
     *
     * @throws FtpException
     * @throws ReflectionException
     */
    public function testListDirErr(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'nlist' => []]);

        self::expectException(FtpException::class);
        $this->adapter->listDir('dir');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::listDirAdvanced
     *
     * @throws FtpException
     * @throws ReflectionException
     */
    public function testAdvancedListDir(): void
    {
        $this->mockSftpFn(
            [
                'isConnected' => TRUE, 'rawlist' =>
                [
                    ['filename' => 'name1', 'size' => 1, 'mtime' => '222'],
                    ['filename' => 'el2', 'size' => 2, 'mtime' => '222'],
                ],
            ]
        );

        self::assertEquals(
            [
                0 => [
                    'name' => 'name1',
                    'path' => 'dir/name1',
                    'size' => 1,
                    'time' => '222',
                ],
                1 => [
                    'name' => 'el2',
                    'path' => 'dir/el2',
                    'size' => 2,
                    'time' => '222',
                ],
            ],
            $this->adapter->listDirAdvanced('dir')
        );
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::listDirAdvanced
     *
     * @throws FtpException
     * @throws ReflectionException
     */
    public function testListDirAdvanceErr(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'nlist' => []]);

        self::expectException(FtpException::class);
        $this->adapter->listDirAdvanced('dir');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::dirExists()
     * @throws FtpException
     * @throws ReflectionException
     */
    public function testDirExists(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'is_dir' => TRUE]);

        self::assertTRUE($this->adapter->dirExists('dir'));
    }

    /**
     * @covers  \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::makeDir()
     *
     * @throws FtpException
     * @throws ReflectionException
     */
    public function testMakeDir(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'mkdir' => TRUE]);

        $this->adapter->makeDir('dir');
        self::assertTrue(TRUE);
    }

    /**
     * @covers  \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::makeDir()
     *
     * @throws FtpException
     * @throws ReflectionException
     */
    public function testMakeDirErr(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'mkdir' => FALSE]);

        self::expectException(FtpException::class);
        $this->adapter->makeDir('dir');
    }

    /**
     * @covers  \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::makeDirRecursive()
     * @covers  \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::isFile()
     *
     * @throws FtpException
     * @throws ReflectionException
     */
    public function testMakeDirRecursive(): void
    {
        $this->mockSftpFn(
            [
                'isConnected' => TRUE, 'pwd' => '/path/', 'chdir' => FALSE, 'is_file' => FALSE, 'mkdir' => TRUE,
            ]
        );

        $this->adapter->makeDirRecursive('dir');
        self::assertTrue(TRUE);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::remove()
     *
     * @throws FtpException
     * @throws ReflectionException
     */
    public function testRemove(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'delete' => TRUE]);

        $this->adapter->remove('file');
        self::assertTrue(TRUE);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::remove()
     *
     * @throws FtpException
     * @throws ReflectionException
     */
    public function testRemoveErr(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'delete' => FALSE]);

        self::expectException(FtpException::class);
        $this->adapter->remove('file');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter::getResource()
     * @throws FtpException
     * @throws ReflectionException
     */
    public function testGetResourceErr(): void
    {
        $this->mockSftpFn(['isConnected' => FALSE, 'delete' => TRUE]);

        self::expectException(FtpException::class);
        $this->adapter->remove('file');
    }

    /**
     * @return FtpConfig
     */
    private function config(): FtpConfig
    {
        return new FtpConfig('host', FALSE, 222, 5, 'guest', 'guest');
    }

    /**
     * @param mixed[] $fns
     *
     * @throws ReflectionException
     */
    private function mockSftpFn(array $fns): void
    {
        foreach ($fns as $key => $value) {
            $this->sftp->expects(self::any())->method($key)->willReturn($value);
        }

        $this->setProperty($this->adapter, 'sftp', $this->sftp);
    }

}
