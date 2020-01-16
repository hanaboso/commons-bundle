<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\FileTypes;

/**
 * Class FileTypesTest
 *
 * @package CommonsBundleTests\Integration\FileStorage
 */
final class FileTypesTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\FileTypes::fromExtension
     * @covers \Hanaboso\CommonsBundle\FileStorage\FileTypes::fromFilename
     *
     * @throws FileStorageException
     */
    public function testFromFilename(): void
    {
        self::assertEquals('text/csv', FileTypes::fromFilename('data.csv'));

        self::expectException(FileStorageException::class);
        FileTypes::fromExtension('abc');
    }

}
