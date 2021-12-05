<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Database\Locator\DatabaseManagerLocator;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Document\File;
use Hanaboso\CommonsBundle\FileStorage\Driver\FileStorageDriverInterface;
use Hanaboso\CommonsBundle\FileStorage\Driver\FileStorageDriverLocator;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileInfoDto;
use Hanaboso\CommonsBundle\FileStorage\FileStorage;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class FileStorageTest
 *
 * @package CommonsBundleTests\Integration\FileStorage
 */
final class FileStorageTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto
     * @covers \Hanaboso\CommonsBundle\FileStorage\FileStorage::saveFileFromContent()
     * @covers \Hanaboso\CommonsBundle\FileStorage\FileStorage::getFileStorage()
     * @covers \Hanaboso\CommonsBundle\FileStorage\FileStorage::deleteFile()
     * @covers \Hanaboso\CommonsBundle\FileStorage\FileStorage
     *
     * @throws Exception
     */
    public function testFileStorage(): void
    {
        $storage = $this->mockStorageService();
        $dto     = new FileContentDto('test_content', 'csv', 'test_name');

        $file = $storage->saveFileFromContent($dto);
        self::assertEquals('test_name', $file->getFilename());
        self::assertEquals('fileUrl', $file->getFileUrl());
        self::assertEquals('7', $file->getSize());
        self::assertNotEmpty($file->getStorageType());

        $content = $storage->getFileStorage($file);
        self::assertEquals('test_content', $content->getContent());

        $storage->deleteFile($file);
        $file = $this->dm->getRepository(File::class)->find($file->getId());
        self::assertNull($file);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\FileStorage::getFileDocument
     *
     * @throws FileStorageException
     * @throws Exception
     */
    public function testGetFileDocument(): void
    {
        $storage = $this->mockStorageService();
        $file    = new File();
        $this->pfd($file);

        self::assertInstanceOf(File::class, $storage->getFileDocument($file->getId()));
        self::expectException(FileStorageException::class);
        $storage->getFileDocument('1');
    }

    /**
     * @return FileStorage
     *
     * @throws Exception
     */
    private function mockStorageService(): FileStorage
    {
        /** @var FileStorageDriverInterface|MockObject $driver */
        $driver = self::createPartialMock(FileStorageDriverInterface::class, ['save', 'delete', 'get']);
        $driver->expects(self::any())->method('save')->willReturn(new FileInfoDto('fileUrl', '7'));
        $driver->expects(self::any())->method('delete');
        $driver->expects(self::any())->method('get')->willReturn('test_content');

        /** @var DatabaseManagerLocator $managerLocator */
        $managerLocator = self::getContainer()->get('hbpf.database_manager_locator');

        return new FileStorage(
            new FileStorageDriverLocator($driver, $driver, $driver),
            $managerLocator,
            File::class,
        );
    }

}
