<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Ftp\Adapter;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter;
use Hanaboso\CommonsBundle\Transport\Ftp\Exception\FtpException;
use Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use phpmock\phpunit\PHPMock;
use phpseclib3\Net\SFTP;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class SftpAdapterTest
 *
 * @package CommonsBundleTests\Unit\Transport\Ftp\Adapter
 */
#[CoversClass(SftpAdapter::class)]
final class SftpAdapterTest extends KernelTestCaseAbstract
{

    use PHPMock;
    use PrivateTrait;
    use CustomAssertTrait;

    /**
     * @var SftpAdapter
     */
    private SftpAdapter $adapter;

    /**
     * @var SFTP|MockObject
     */
    private $sftp;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->sftp    = self::createMock(SFTP::class);
        $this->adapter = new SftpAdapter();
    }

    /**
     * @throws Exception
     */
    public function testConnection(): void
    {
        $this->adapter->connect($this->config());

        self::assertFake();
    }

    /**
     * @throws Exception
     */
    public function testLogin(): void
    {
        $this->mockSftpFn(['login' => TRUE]);
        $this->adapter->login($this->config());

        self::assertFake();
    }

    /**
     * @throws Exception
     */
    public function testLoginErr(): void
    {
        $this->mockSftpFn(['login' => FALSE]);

        self::expectException(FtpException::class);
        $this->adapter->login($this->config());
    }

    /**
     * @throws Exception
     */
    public function testDisconnect(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE]);
        $this->adapter->disconnect();

        self::assertFake();
    }

    /**
     * @throws Exception
     */
    public function testUploadFile(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'put' => TRUE]);

        $this->adapter->uploadFile('file', 'path');
        self::assertFake();
    }

    /**
     * @throws Exception
     */
    public function testUploadFileErr(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'put' => FALSE]);

        self::expectException(FtpException::class);
        $this->adapter->uploadFile('file', 'path');
    }

    /**
     * @throws Exception
     */
    public function testDownloadFile(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'get' => TRUE]);

        $this->adapter->downloadFile('file', 'path');
        self::assertFake();
    }

    /**
     * @throws Exception
     */
    public function testDownloadFileErr(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'get' => FALSE]);

        self::expectException(FtpException::class);
        $this->adapter->downloadFile('file', 'path');
    }

    /**
     * @throws Exception
     */
    public function testListDir(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'nlist' => ['el1', 'el2']]);

        self::assertEquals(
            [
                0 => 'el1',
                1 => 'el2',
            ],
            $this->adapter->listDir('dir'),
        );
    }

    /**
     * @throws Exception
     */
    public function testListDirErr(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'nlist' => []]);

        self::expectException(FtpException::class);
        $this->adapter->listDir('dir');
    }

    /**
     * @throws Exception
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
            ],
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
            $this->adapter->listDirAdvanced('dir'),
        );
    }

    /**
     * @throws Exception
     */
    public function testListDirAdvanceErr(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'nlist' => []]);

        self::expectException(FtpException::class);
        $this->adapter->listDirAdvanced('dir');
    }

    /**
     * @throws Exception
     */
    public function testDirExists(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'is_dir' => TRUE]);

        self::assertTrue($this->adapter->dirExists('dir'));
    }

    /**
     * @throws Exception
     */
    public function testMakeDir(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'mkdir' => TRUE]);

        $this->adapter->makeDir('dir');
        self::assertFake();
    }

    /**
     * @throws Exception
     */
    public function testMakeDirErr(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'mkdir' => FALSE]);

        self::expectException(FtpException::class);
        $this->adapter->makeDir('dir');
    }

    /**
     * @throws Exception
     */
    public function testMakeDirRecursive(): void
    {
        $this->mockSftpFn(
            [
            'chdir' => FALSE,
            'isConnected' => TRUE,
            'is_file' => FALSE,
            'mkdir' => TRUE,
            'pwd' => '/path/',
            ],
        );

        $this->adapter->makeDirRecursive('dir');
        self::assertFake();
    }

    /**
     * @throws Exception
     */
    public function testRename(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'rename' => TRUE]);

        self::assertTrue($this->adapter->rename('oldName', 'newName'));
    }

    /**
     * @throws Exception
     */
    public function testFileExits(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'file_exists' => TRUE]);

        self::assertTrue($this->adapter->fileExists('/tmp/test.txt'));
    }

    /**
     * @throws Exception
     */
    public function testRemove(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'delete' => TRUE]);

        $this->adapter->remove('file');
        self::assertFake();
    }

    /**
     * @throws Exception
     */
    public function testRemoveErr(): void
    {
        $this->mockSftpFn(['isConnected' => TRUE, 'delete' => FALSE]);

        self::expectException(FtpException::class);
        $this->adapter->remove('file');
    }

    /**
     * @throws Exception
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
     * @throws Exception
     */
    private function mockSftpFn(array $fns): void
    {
        foreach ($fns as $key => $value) {
            $this->sftp->expects(self::any())->method($key)->willReturn($value);
        }

        $this->setProperty($this->adapter, 'sftp', $this->sftp);
    }

}
