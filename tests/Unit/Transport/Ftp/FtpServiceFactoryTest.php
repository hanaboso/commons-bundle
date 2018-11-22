<?php declare(strict_types=1);

namespace Tests\Unit\Transport\Ftp;

use Exception;
use Hanaboso\CommonsBundle\Transport\Ftp\Adapter\FtpAdapter;
use Hanaboso\CommonsBundle\Transport\Ftp\Adapter\SftpAdapter;
use Hanaboso\CommonsBundle\Transport\Ftp\Exception\FtpException;
use Hanaboso\CommonsBundle\Transport\Ftp\FtpServiceFactory;
use Tests\KernelTestCaseAbstract;

/**
 * Class FtpServiceFactoryTest
 *
 * @package Tests\Unit\Transport\Ftp
 */
final class FtpServiceFactoryTest extends KernelTestCaseAbstract
{

    /**
     * @covers FtpServiceFactory::getFtpService().
     * @throws Exception
     */
    public function testGetServiceFtp(): void
    {
        /** @var FtpServiceFactory $factory */
        $factory = $this->c->get('hbpf.ftp.service.factory');
        $service = $factory->getFtpService(FtpServiceFactory::ADAPTER_FTP);

        self::assertInstanceOf(FtpAdapter::class, $service->getAdapter());
    }

    /**
     * @covers FtpServiceFactory::getFtpService()
     * @throws Exception
     */
    public function testGetServiceSftp(): void
    {
        /** @var FtpServiceFactory $factory */
        $factory = $this->c->get('hbpf.ftp.service.factory');
        $service = $factory->getFtpService(FtpServiceFactory::ADAPTER_SFTP);

        self::assertInstanceOf(SftpAdapter::class, $service->getAdapter());
    }

    /**
     * @covers FtpServiceFactory::getFtpService()
     */
    public function testGetServiceUnknown(): void
    {
        $factory = $this->c->get('hbpf.ftp.service.factory');

        self::expectException(FtpException::class);
        self::expectExceptionCode(FtpException::UNKNOWN_ADAPTER_TYPE);

        $factory->getFtpService('abc');
    }

}