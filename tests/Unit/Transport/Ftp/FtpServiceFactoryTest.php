<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Ftp;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Transport\Ftp\Adapter\FtpAdapter;
use Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter;
use Hanaboso\CommonsBundle\Transport\Ftp\Exception\FtpException;
use Hanaboso\CommonsBundle\Transport\Ftp\FtpServiceFactory;

/**
 * Class FtpServiceFactoryTest
 *
 * @package CommonsBundleTests\Unit\Transport\Ftp
 */
final class FtpServiceFactoryTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\FtpServiceFactory::getFtpService()
     *
     * @throws Exception
     */
    public function testGetServiceFtp(): void
    {
        /** @var FtpServiceFactory $factory */
        $factory = self::$container->get('hbpf.ftp.service.factory');
        $service = $factory->getFtpService(FtpServiceFactory::ADAPTER_FTP);

        self::assertInstanceOf(FtpAdapter::class, $service->getAdapter());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\FtpServiceFactory::getFtpService()
     *
     * @throws Exception
     */
    public function testGetServiceSftp(): void
    {
        /** @var FtpServiceFactory $factory */
        $factory = self::$container->get('hbpf.ftp.service.factory');
        $service = $factory->getFtpService(FtpServiceFactory::ADAPTER_SFTP);

        self::assertInstanceOf(SftpAdapter::class, $service->getAdapter());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Ftp\FtpServiceFactory::getFtpService()
     *
     * @throws FtpException
     */
    public function testGetServiceUnknown(): void
    {
        /** @var FtpServiceFactory $factory */
        $factory = self::$container->get('hbpf.ftp.service.factory');

        self::expectException(FtpException::class);
        self::expectExceptionCode(FtpException::UNKNOWN_ADAPTER_TYPE);

        $factory->getFtpService('abc');
    }

}
