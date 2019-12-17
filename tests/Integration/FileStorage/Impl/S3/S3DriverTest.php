<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Impl\S3;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Driver\Impl\S3\S3Driver;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileInfoDto;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class S3DriverTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Impl\S3
 */
final class S3DriverTest extends KernelTestCaseAbstract
{

    /**
     * @var S3Driver
     */
    private S3Driver $driver;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @var S3Driver $containerDriver */
        $containerDriver = self::$container->get('hbpf.file_storage.driver.s3');
        $this->driver    = $containerDriver;
    }

    /**
     * @throws Exception
     */
    public function testDriver(): void
    {
        $path         = sprintf('%s/data/Attachment.jpeg', __DIR__);
        $uploadedFile = new UploadedFile($path, '');

        $file = $this->driver->save((string) file_get_contents((string) $uploadedFile->getRealPath()));

        self::assertInstanceOf(FileInfoDto::class, $file);
        self::assertEquals(file_get_contents($path), $this->driver->get($file->getUrl()));

        self::expectException(FileStorageException::class);
        self::expectExceptionCode(FileStorageException::FILE_NOT_FOUND);
        $this->driver->delete($file->getUrl());
        $this->driver->get($file->getUrl());
    }

}
