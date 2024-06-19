<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Document;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Document\File;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class FileTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Document
 */
#[CoversClass(File::class)]
final class FileTest extends DatabaseTestCaseAbstract
{

    /**
     * @throws Exception
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
     * @throws Exception
     */
    public function testFileFormatException(): void
    {
        $file = new File();
        self::expectException(FileStorageException::class);
        $file->setFileFormat('png');
    }

    /**
     * @throws Exception
     */
    public function testStorageTypeException(): void
    {
        $file = new File();
        self::expectException(FileStorageException::class);
        $file->setStorageType('imminent');
    }

}
