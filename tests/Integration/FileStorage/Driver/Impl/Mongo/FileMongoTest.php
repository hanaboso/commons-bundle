<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Driver\Impl\Mongo;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\FileMongo;
use Hanaboso\Utils\Date\DateTimeUtils;
use Hanaboso\Utils\Exception\DateTimeException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class FileMongoTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Driver\Impl\Mongo
 */
#[CoversClass(FileMongo::class)]
final class FileMongoTest extends DatabaseTestCaseAbstract
{

    /**
     * @throws DateTimeException
     */
    public function testFileMongo(): void
    {
        $mongo = (new FileMongo())
            ->setFilename('name')
            ->setChunkSize(100)
            ->setLength(100)
            ->setUploadDate(DateTimeUtils::getUtcDateTime());

        $mongo->getUploadDate();
        self::assertEquals('name', $mongo->getFilename());
        self::assertEquals(100, $mongo->getChunkSize());
        self::assertEquals(100, $mongo->getLength());
    }

}
