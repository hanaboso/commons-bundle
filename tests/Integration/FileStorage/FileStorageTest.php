<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: david.horacek
 * Date: 8/22/17
 * Time: 10:16 AM
 */

namespace Tests\Integration\FileStorage;

use Exception;
use Hanaboso\CommonsBundle\FileStorage\Document\File;
use Hanaboso\CommonsBundle\FileStorage\Driver\FileStorageDriverInterface;
use Hanaboso\CommonsBundle\FileStorage\Driver\FileStorageDriverLocator;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileInfoDto;
use Hanaboso\CommonsBundle\FileStorage\FileStorage;
use PHPUnit_Framework_MockObject_MockObject;
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
        /** @var FileStorageDriverInterface|PHPUnit_Framework_MockObject_MockObject $driver */
        $driver = $this->createPartialMock(FileStorageDriverInterface::class, ['save', 'delete', 'get']);
        $driver->method('save')->willReturn(new FileInfoDto('fileUrl', '7'));
        $driver->method('delete')->willReturn('');
        $driver->method('get')->willReturn('test_content');

        $locator = new FileStorageDriverLocator($driver, $driver, $driver);

        $storage = new FileStorage($locator, $this->c->get('hbpf.database_manager_locator'),
            'Hanaboso\CommonsBundle\FileStorage\Document\File');

        return $storage;
    }

}