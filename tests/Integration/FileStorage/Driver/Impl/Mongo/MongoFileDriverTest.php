<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Driver\Impl\Mongo;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver;
use Hanaboso\CommonsBundle\FileStorage\PathGenerator\HashPathGenerator;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class MongoFileDriverTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Driver\Impl\Mongo
 */
#[CoversClass(MongoFileDriver::class)]
final class MongoFileDriverTest extends DatabaseTestCaseAbstract
{

    /**
     * @var MongoFileDriver
     */
    private MongoFileDriver $driver;

    /**
     * @throws Exception
     */
    public function testFileStorage(): void
    {
        $res = $this->driver->save('test_content', 'test_name');
        $this->dm->clear();
        $this->driver->setPathGenerator(new HashPathGenerator());

        $fileContent = $this->driver->get($res->getUrl());
        self::assertEquals('test_content', $fileContent);

        $this->dm->clear();
        $this->driver->delete($res->getUrl());
        self::expectException(FileStorageException::class);
        self::expectExceptionCode(FileStorageException::FILE_NOT_FOUND);
        $this->driver->get($res->getUrl());
    }

    /**
     * @throws FileStorageException
     */
    public function testSaverErr(): void
    {
        $this->dm->close();
        self::expectException(FileStorageException::class);
        $this->driver->save('data');
    }

    /**
     * @throws FileStorageException
     */
    public function testDeleteErr(): void
    {
        $this->dm->close();
        self::expectException(FileStorageException::class);
        $this->driver->delete('data');
    }

    /**
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

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @var MongoFileDriver $containerDriver */
        $containerDriver = self::getContainer()->get('hbpf.file_storage.driver.mongo');
        $this->driver    = $containerDriver;
    }

}
