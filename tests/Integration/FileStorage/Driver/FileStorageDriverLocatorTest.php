<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Driver;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Driver\FileStorageDriverLocator;
use Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\MongoFileDriver;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class FileStorageDriverLocatorTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Driver
 */
#[CoversClass(FileStorageDriverLocator::class)]
final class FileStorageDriverLocatorTest extends DatabaseTestCaseAbstract
{

    /**
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
