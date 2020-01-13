<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Driver\Impl\Mongo;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver;
use Hanaboso\CommonsBundle\FileStorage\PathGenerator\HashPathGenerator;

/**
 * Class MongoFileDriverTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Driver\Impl\Mongo
 */
final class MongoFileDriverTest extends DatabaseTestCaseAbstract
{

    /**
     * @var MongoFileDriver
     */
    private MongoFileDriver $driver;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @var MongoFileDriver $containerDriver */
        $containerDriver = self::$container->get('hbpf.file_storage.driver.mongo');
        $this->driver    = $containerDriver;
    }

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver::save()
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver::get()
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver::delete()
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver::generatePath()
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver::setPathGenerator()
     *
     * @throws Exception
     */
    public function testFileStorage(): void
    {
        $res = $this->driver->save('test_content', 'test_name');
        $this->dm->clear();
        $this->driver->setPathGenerator(new HashPathGenerator());

        /** @var FileMongoTest $fileContent */
        $fileContent = $this->driver->get($res->getUrl());
        self::assertEquals('test_content', $fileContent);

        $this->dm->clear();
        $this->driver->delete($res->getUrl());
        self::expectException(FileStorageException::class);
        self::expectExceptionCode(FileStorageException::FILE_NOT_FOUND);
        $this->driver->get($res->getUrl());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver::save()
     *
     * @throws FileStorageException
     */
    public function testSaverErr(): void
    {
        $this->dm->close();
        self::expectException(FileStorageException::class);
        $this->driver->save('data');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver::delete
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver::getDocument
     * @throws FileStorageException
     */
    public function testDeleteErr(): void
    {
        $this->dm->close();
        self::expectException(FileStorageException::class);
        $this->driver->delete('data');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver::delete
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver::save
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver::getDocument
     * @throws FileStorageException
     * @throws Exception
     */
    public function testDeleteFileExistsErr(): void
    {
        $fileInfo = $this->driver->save('data');

        $this->dm->close();
        self::expectException(FileStorageException::class);
        $this->driver->delete($fileInfo->getUrl());
    }

}
