<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Driver\Impl\S3;

use Aws\S3\S3Client;
use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Driver\Impl\S3\S3Driver;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileInfoDto;
use ReflectionException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class S3DriverTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Driver\Impl\S3
 */
final class S3DriverTest extends KernelTestCaseAbstract
{

    private const FAKE_AWS_CONNECTION_ARGS = [
        'region'      => 's-west-2',
        'version'     => '2006-03-01',
        'credentials' => [
            'key'    => 'key',
            'secret' => 'secret',
        ],
        'endpoint'    => 'ttps://vpce-0f89a33420c193abc-bluzidnv',
    ];

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
        self::assertIsString($file->getSize());

        self::expectException(FileStorageException::class);
        self::expectExceptionCode(FileStorageException::FILE_NOT_FOUND);
        $this->driver->delete($file->getUrl());
        $this->driver->get($file->getUrl());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\S3\S3Driver::save
     *
     * @throws FileStorageException
     * @throws ReflectionException
     */
    public function testDriverErr(): void
    {
        $path         = sprintf('%s/data/Attachment.jpeg', __DIR__);
        $uploadedFile = new UploadedFile($path, '');

        $this->setProperty($this->driver, 'client', new S3Client(self::FAKE_AWS_CONNECTION_ARGS));

        self::expectException(FileStorageException::class);
        $this->driver->save((string) file_get_contents((string) $uploadedFile->getRealPath()));
    }

}
