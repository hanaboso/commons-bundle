<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Document;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Document\File;

/**
 * Class FileTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Document
 */
final class FileTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Document\File::getFilename
     * @covers \Hanaboso\CommonsBundle\FileStorage\Document\File::setFilename
     * @covers \Hanaboso\CommonsBundle\FileStorage\Document\File::setFileFormat
     * @covers \Hanaboso\CommonsBundle\FileStorage\Document\File::getFileFormat
     * @covers \Hanaboso\CommonsBundle\FileStorage\Document\File::getMimeType
     * @covers \Hanaboso\CommonsBundle\FileStorage\Document\File::getFileUrl
     * @covers \Hanaboso\CommonsBundle\FileStorage\Document\File::setFileUrl
     * @covers \Hanaboso\CommonsBundle\FileStorage\Document\File::getSize
     * @covers \Hanaboso\CommonsBundle\FileStorage\Document\File::setSize
     * @covers \Hanaboso\CommonsBundle\FileStorage\Document\File::getStorageType
     * @covers \Hanaboso\CommonsBundle\FileStorage\Document\File::setStorageType
     */
    public function testFile(): void
    {
        /** @var File $file */
        $file = (new File())
            ->setFilename('filename')
            ->setFileFormat('csv')
            ->setFileUrl('/path/')
            ->setSize('1 kb')
            ->setStorageType('persistent');

        self::assertEquals('csv', $file->getFileFormat());
        self::assertEquals('filename', $file->getFilename());
        self::assertEquals('text/csv', $file->getMimeType());
        self::assertEquals('/path/', $file->getFileUrl());
        self::assertEquals('1 kb', $file->getSize());
        self::assertEquals('persistent', $file->getStorageType());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Document\File::setFileFormat
     *
     * @throws Exception
     */
    public function testFileFormatException(): void
    {
        $file = new File();
        self::expectException(FileStorageException::class);
        $file->setFileFormat('png');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Document\File::setStorageType
     *
     * @throws Exception
     */
    public function testStorageTypeException(): void
    {
        $file = new File();
        self::expectException(FileStorageException::class);
        $file->setStorageType('imminent');
    }

}
