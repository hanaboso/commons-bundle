<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Impl\Mongo;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\FileMongo;
use Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver;

/**
 * Class MongoFileDriverTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Impl\Mongo
 */
final class MongoFileDriverTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver::save()
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver::get()
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver::delete()
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver::generatePath()
     *
     * @throws Exception
     */
    public function testFileStorage(): void
    {
        /** @var MongoFileDriver $driver */
        $driver = self::$container->get('hbpf.file_storage.driver.mongo');

        $res = $driver->save('test_content', 'test_name');
        $this->dm->clear();

        /** @var FileMongo $fileContent */
        $fileContent = $driver->get($res->getUrl());
        self::assertEquals('test_content', $fileContent);

        $this->dm->clear();
        $driver->delete($res->getUrl());
        self::expectException(FileStorageException::class);
        self::expectExceptionCode(FileStorageException::FILE_NOT_FOUND);
        $driver->get($res->getUrl());
    }

}
