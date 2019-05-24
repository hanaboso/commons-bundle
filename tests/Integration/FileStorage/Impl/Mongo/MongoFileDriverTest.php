<?php declare(strict_types=1);

namespace Tests\Integration\FileStorage\Impl\Mongo;

use Exception;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\FileMongo;
use Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver;
use Tests\DatabaseTestCaseAbstract;

/**
 * Class MongoFileDriverTest
 *
 * @package Tests\Integration\FileStorage\Impl\Mongo
 */
final class MongoFileDriverTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers MongoFileDriver::save()
     * @covers MongoFileDriver::get()
     * @covers MongoFileDriver::delete()
     * @covers MongoFileDriver::generatePath()
     * @throws Exception
     */
    public function testFileStorage(): void
    {
        /** @var MongoFileDriver $driver */
        $driver = self::$container->get('hbpf.file_storage.driver.mongo');

        $res = $driver->save('test_content', 'test_name');
        $this->dm->clear();

        /** @var FileMongo $file */
        $fileContent = $driver->get($res->getUrl());
        self::assertEquals('test_content', $fileContent);

        $this->dm->clear();
        $driver->delete($res->getUrl());
        self::expectException(FileStorageException::class);
        self::expectExceptionCode(FileStorageException::FILE_NOT_FOUND);
        $driver->get($res->getUrl());
    }

}
