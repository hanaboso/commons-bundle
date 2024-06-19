<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\FileTypes;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class FileTypesTest
 *
 * @package CommonsBundleTests\Integration\FileStorage
 */
#[CoversClass(FileTypes::class)]
final class FileTypesTest extends DatabaseTestCaseAbstract
{

    /**
     * @throws FileStorageException
     */
    public function testFromFilename(): void
    {
        self::assertEquals('text/csv', FileTypes::fromFilename('data.csv'));

        self::expectException(FileStorageException::class);
        FileTypes::fromExtension('abc');
    }

}
