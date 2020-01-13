<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Driver\Impl\Mongo;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use DateTimeInterface;
use Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\FileMongo;
use Hanaboso\Utils\Date\DateTimeUtils;
use Hanaboso\Utils\Exception\DateTimeException;

/**
 * Class FileMongoTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Driver\Impl\Mongo
 */
final class FileMongoTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\FileMongo
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\FileMongo::getFilename
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\FileMongo::setFilename
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\FileMongo::getUploadDate
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\FileMongo::setUploadDate
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\FileMongo::getLength
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\FileMongo::setLength
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\FileMongo::getChunkSize
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo\FileMongo::setChunkSize
     *
     * @throws DateTimeException
     */
    public function testFileMongo(): void
    {
        $mongo = (new FileMongo())
            ->setFilename('name')
            ->setChunkSize(100)
            ->setLength(100)
            ->setUploadDate(DateTimeUtils::getUtcDateTime());

        self::assertEquals('name', $mongo->getFilename());
        self::assertEquals(100, $mongo->getChunkSize());
        self::assertEquals(100, $mongo->getLength());
        self::assertInstanceOf(DateTimeInterface::class, $mongo->getUploadDate());
    }

}