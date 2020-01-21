<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Ftp;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig;

/**
 * Class FtpConfigTest
 *
 * @package CommonsBundleTests\Unit\Transport\Ftp
 */
final class FtpConfigTest extends KernelTestCaseAbstract
{

    /**
     * @var FtpConfig
     */
    private FtpConfig $config;

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig::getHost
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig
     */
    public function testGetHost(): void
    {
        self::assertEquals('host', $this->config->getHost());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig::isSsl
     */
    public function testIsSsl(): void
    {
        self::assertTrue($this->config->isSsl());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig::getPort
     */
    public function testGetPort(): void
    {
        self::assertEquals(222, $this->config->getPort());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig::getTimeout
     */
    public function testGetTimeout(): void
    {
        self::assertEquals(5, $this->config->getTimeout());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig::getUsername
     */
    public function testGetUsername(): void
    {
        self::assertEquals('guest', $this->config->getUsername());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig::getPassword
     */
    public function testGetPassword(): void
    {
        self::assertEquals('guest', $this->config->getPassword());
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->config = new FtpConfig('host', TRUE, 222, 5, 'guest', 'guest');
    }

}
