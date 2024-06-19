<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Ftp;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Transport\Ftp\FtpConfig;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class FtpConfigTest
 *
 * @package CommonsBundleTests\Unit\Transport\Ftp
 */
#[CoversClass(FtpConfig::class)]
final class FtpConfigTest extends KernelTestCaseAbstract
{

    /**
     * @var FtpConfig
     */
    private FtpConfig $config;

    /**
     * @return void
     */
    public function testGetHost(): void
    {
        self::assertEquals('host', $this->config->getHost());
    }

    /**
     * @return void
     */
    public function testIsSsl(): void
    {
        self::assertTrue($this->config->isSsl());
    }

    /**
     * @return void
     */
    public function testGetPort(): void
    {
        self::assertEquals(222, $this->config->getPort());
    }

    /**
     * @return void
     */
    public function testGetTimeout(): void
    {
        self::assertEquals(5, $this->config->getTimeout());
    }

    /**
     * @return void
     */
    public function testGetUsername(): void
    {
        self::assertEquals('guest', $this->config->getUsername());
    }

    /**
     * @return void
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
