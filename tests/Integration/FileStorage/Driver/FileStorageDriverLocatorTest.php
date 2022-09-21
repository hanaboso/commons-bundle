<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Driver;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
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
     * @throws Exception
     */
    public function testGet(): void
    {
        $mongo            = self::getContainer()->get('hbpf.file_storage.driver.mongo');
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
