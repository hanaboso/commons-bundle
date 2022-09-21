<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Ftp;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Transport\Ftp\Adapter\FtpAdapter;
use Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter;
use Hanaboso\CommonsBundle\Transport\Ftp\Exception\FtpException;
use Hanaboso\CommonsBundle\Transport\Ftp\FtpServiceFactory;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use Monolog\Logger;

/**
 * Class FtpServiceFactoryTest
 *
 * @package CommonsBundleTests\Unit\Transport\Ftp
 */
final class FtpServiceFactoryTest extends KernelTestCaseAbstract
{

    use CustomAssertTrait;

    /**
     * @var FtpServiceFactory
     */
    private $factory;

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\FtpServiceFactory::getFtpService()
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\FtpServiceFactory
     *
     * @throws Exception
     */
    public function testGetServiceFtp(): void
    {
        $service = $this->factory->getFtpService(FtpServiceFactory::ADAPTER_FTP);

        self::assertInstanceOf(FtpAdapter::class, $service->getAdapter());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\FtpServiceFactory::getFtpService()
     *
     * @throws Exception
     */
    public function testGetServiceSftp(): void
    {
        $service = $this->factory->getFtpService(FtpServiceFactory::ADAPTER_SFTP);

        self::assertInstanceOf(SftpAdapter::class, $service->getAdapter());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\FtpServiceFactory::getFtpService()
     *
     * @throws FtpException
     */
    public function testGetServiceUnknown(): void
    {
        self::expectException(FtpException::class);
        self::expectExceptionCode(FtpException::UNKNOWN_ADAPTER_TYPE);

        $this->factory->getFtpService('abc');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\FtpServiceFactory::setLogger
     */
    public function testSetLogger(): void
    {
        $this->factory->setLogger(new Logger('logger'));
        self::assertFake();
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = self::getContainer()->get('hbpf.ftp.service.factory');
    }

}
