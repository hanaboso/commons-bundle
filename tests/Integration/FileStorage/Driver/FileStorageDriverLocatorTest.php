<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Driver;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Driver\FileStorageDriverLocator;
use Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver;

/**
 * Class FileStorageDriverLocatorTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Driver
 */
final class FileStorageDriverLocatorTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\FileStorageDriverLocator::get
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\FileStorageDriverLocator
     *
     * @throws FileStorageException
     */
    public function testGet(): void
    {
        $mongo            = self::$container->get('hbpf.file_storage.driver.mongo');
        $fileDrive        = new FileStorageDriverLocator($mongo, $mongo, $mongo);
        $persistentDriver = $fileDrive->get('persistent');
        $temporaryDriver  = $fileDrive->get('temporary');
        $publicDriver     = $fileDrive->get('public');

        self::assertInstanceOf(MongoFileDriver::class, $persistentDriver);
        self::assertInstanceOf(MongoFileDriver::class, $temporaryDriver);
        self::assertInstanceOf(MongoFileDriver::class, $publicDriver);

        self::expectException(FileStorageException::class);
        $fileDrive->get('private');
    }

}