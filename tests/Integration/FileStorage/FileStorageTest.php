<?php declare(strict_types=1);

namespace Tests\Integration\FileStorage;

use Exception;
use Hanaboso\CommonsBundle\DatabaseManager\DatabaseManagerLocator;
use Hanaboso\CommonsBundle\FileStorage\Document\File;
use Hanaboso\CommonsBundle\FileStorage\Driver\FileStorageDriverInterface;
use Hanaboso\CommonsBundle\FileStorage\Driver\FileStorageDriverLocator;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileInfoDto;
use Hanaboso\CommonsBundle\FileStorage\FileStorage;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\DatabaseTestCaseAbstract;

/**
 * Class FileStorageTest
 *
 * @package Tests\Integration\FileStorage
 */
final class FileStorageTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers FileStorage::saveFileFromContent()
     * @covers FileStorage::getFileStorage()
     * @covers FileStorage::deleteFile()
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
     * @return FileStorage
     *
     * @throws Exception
     */
    private function mockStorageService(): FileStorage
    {
        /** @var FileStorageDriverInterface|MockObject $driver */
        $driver = $this->createPartialMock(FileStorageDriverInterface::class, ['save', 'delete', 'get']);
        $driver->expects($this->any())->method('save')->willReturn(new FileInfoDto('fileUrl', '7'));
        $driver->expects($this->any())->method('delete')->willReturn('');
        $driver->expects($this->any())->method('get')->willReturn('test_content');

        /** @var DatabaseManagerLocator $managerLocator */
        $managerLocator = self::$container->get('hbpf.database_manager_locator');

        return new FileStorage(
            new FileStorageDriverLocator($driver, $driver, $driver),
            $managerLocator,
            'Hanaboso\CommonsBundle\FileStorage\Document\File'
        );
    }

}
