<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Ftp\Adapter;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Transport\Ftp\Adapter\FtpAdapter;
use Hanaboso\CommonsBundle\Transport\Ftp\Exception\FtpException;
use Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class FtpAdapterTest
 *
 * @package CommonsBundleTests\Unit\Transport\Ftp\Adapter
 */
#[CoversClass(FtpAdapter::class)]
final class FtpAdapterTest extends KernelTestCaseAbstract
{

    use PHPMock;
    use PrivateTrait;
    use CustomAssertTrait;

    /**
     * @var FtpAdapter
     */
    private FtpAdapter $ftp;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->ftp = new FtpAdapter();
        $this->setProperty($this->ftp, 'ftp', TRUE);
    }

    /**
     * @throws FtpException
     */
    public function testConnect(): void
    {
        $this->mockFtpFunction('is_bool', FALSE);
        $this->mockFtpFunction('ftp_ssl_connect', TRUE);

        $this->ftp->connect(new FtpConfig('host', TRUE, 222, 5, 'guest', 'guest'));
        self::assertFake();
    }

    /**
     * @throws FtpException
     */
    public function testConnectErr(): void
    {
        $this->mockFtpFunction('ftp_connect', TRUE);

        self::expectException(FtpException::class);
        $this->ftp->connect($this->config());
    }

    /**
     * @throws FtpException
     */
    public function testLogin(): void
    {
        $this->mockFtpFunction('ftp_connect', TRUE);
        $this->mockFtpFunction('ftp_login', TRUE);
        $this->mockFtpFunction('ftp_pasv', TRUE);
        $this->mockFtpFunction('is_bool', FALSE);

        $this->ftp->login($this->config());

        self::assertFake();
    }

    /**
     * @throws FtpException
     */
    public function testLoginErr(): void
    {
        $this->mockFtpFunction('is_bool', FALSE);
        $this->mockFtpFunction('ftp_login', FALSE);

        self::expectException(FtpException::class);
        $this->ftp->login($this->config());
    }

    /**
     * @throws FtpException
     */
    public function testDisconnect(): void
    {
        $this->mockFtpFunction('is_bool', FALSE);
        $this->mockFtpFunction('ftp_close', TRUE);

        $this->ftp->disconnect();
    }

    /**
     * @throws FtpException
     */
    public function testDisconnectErr(): void
    {
        $this->mockFtpFunction('is_bool', FALSE);
        $this->mockFtpFunction('ftp_close', FALSE);

        self::expectException(FtpException::class);
        $this->ftp->disconnect();
    }

    /**
     * @throws FtpException
     */
    public function testUploadFile(): void
    {
        $this->mockFtpFunction('ftp_put', TRUE);
        $this->mockFtpFunction('is_bool', FALSE);

        $this->ftp->uploadFile('file', 'local');
    }

    /**
     * @throws FtpException
     */
    public function testUploadFileErr(): void
    {
        $this->mockFtpFunction('ftp_put', FALSE);
        $this->mockFtpFunction('is_bool', FALSE);

        self::expectException(FtpException::class);
        $this->ftp->uploadFile('file', 'local');
    }

    /**
     * @throws FtpException
     */
    public function testDownloadFileErr(): void
    {
        $this->mockFtpFunction('ftp_get', FALSE);
        $this->mockFtpFunction('is_bool', FALSE);

        self::expectException(FtpException::class);
        $this->ftp->downloadFile('file', 'local');
    }

    /**
     * @throws FtpException
     */
    public function testDownloadFile(): void
    {
        $this->mockFtpFunction('ftp_get', TRUE);
        $this->mockFtpFunction('is_bool', FALSE);

        $this->ftp->downloadFile('file', 'local');
        self::assertFake();
    }

    /**
     * @throws FtpException
     */
    public function testListDir(): void
    {
        $this->mockFtpFunction('ftp_nlist', FALSE);
        $this->mockFtpFunction('is_bool', FALSE);

        self::expectException(FtpException::class);
        $this->ftp->listDir('dir');
    }

    /**
     * @throws FtpException
     */
    public function testListDirErr(): void
    {
        $this->mockFtpFunction('ftp_nlist', []);
        $this->mockFtpFunction('is_bool', FALSE);

        self::assertEquals([], $this->ftp->listDir('dir'));
    }

    /**
     * @throws FtpException
     */
    public function testDirExists(): void
    {
        $this->mockFtpFunction('ftp_pwd', 'string');
        $this->mockFtpFunction('is_bool', FALSE);
        $this->mockFtpFunction('ftp_chdir', TRUE);

        self::assertTrue($this->ftp->dirExists('dir'));
    }

    /**
     * @throws FtpException
     */
    public function testDirExistsErr(): void
    {
        $this->mockFtpFunction('ftp_pwd', '/path/');
        $this->mockFtpFunction('is_bool', FALSE);
        $this->mockFtpFunction('ftp_chdir', FALSE);

        self::assertTrue(!$this->ftp->dirExists('dir'));
    }

    /**
     * @throws FtpException
     */
    public function testMakeDir(): void
    {
        $this->mockFtpFunction('ftp_mkdir', TRUE);
        $this->mockFtpFunction('is_bool', FALSE);
        $this->ftp->makeDir('dir');

        self::assertFake();
    }

    /**
     * @throws FtpException
     */
    public function testMakeDirErr(): void
    {
        $this->mockFtpFunction('ftp_mkdir', FALSE);
        $this->mockFtpFunction('is_bool', FALSE);

        self::expectException(FtpException::class);
        $this->ftp->makeDir('dir');
    }

    /**
     * @throws FtpException
     */
    public function testRemove(): void
    {
        $this->mockFtpFunction('ftp_delete', TRUE);
        $this->mockFtpFunction('is_bool', FALSE);

        $this->ftp->remove('file');
        self::assertFake();
    }

    /**
     * @throws FtpException
     */
    public function testRemoveErr(): void
    {
        $this->mockFtpFunction('is_bool', FALSE);
        $this->mockFtpFunction('ftp_delete', FALSE);

        self::expectException(FtpException::class);
        $this->ftp->remove('file');
    }

    /**
     * @throws FtpException
     */
    public function testGetResource(): void
    {
        $this->mockFtpFunction('ftp_delete', TRUE);

        self::expectException(FtpException::class);
        $this->ftp->remove('file');
    }

    /**
     * @throws FtpException
     */
    public function testMakeDirRecursive(): void
    {
        $this->mockFtpFunction('ftp_pwd', '/path/');
        $this->mockFtpFunction('ftp_chdir', FALSE);
        $this->mockFtpFunction('is_bool', FALSE);
        $this->mockFtpFunction('ftp_mkdir', TRUE);
        $this->mockFtpFunction('ftp_size', -1);

        $this->ftp->makeDirRecursive('dir');
        self::assertFake();
    }

    /**
     * @param string $name
     * @param mixed  $result
     */
    private function mockFtpFunction(string $name, $result): void
    {
        $this->getFunctionMock('Hanaboso\CommonsBundle\Transport\Ftp\Adapter', $name)
            ->expects(self::any())
            ->willReturn($result);
    }

    /**
     * @return FtpConfig
     */
    private function config(): FtpConfig
    {
        return new FtpConfig('host', FALSE, 222, 5, 'guest', 'guest');
    }

}
