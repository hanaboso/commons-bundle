<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Dto;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class FileContentDtoTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Dto
 */
#[CoversClass(FileContentDto::class)]
final class FileContentDtoTest extends DatabaseTestCaseAbstract
{

    /**
     * @throws Exception
     */
    public function testFileContentDto(): void
    {
        $dto = (new FileContentDto('data', 'csv', 'name'))
            ->setFilename('name')
            ->setStorageType('persistent')
            ->setContent('data')
            ->setFormat('csv');

        self::assertEquals('name', $dto->getFilename());
        self::assertEquals('persistent', $dto->getStorageType());
        self::assertEquals('data', $dto->getContent());
        self::assertEquals('csv', $dto->getFormat());
    }

    /**
     * @throws Exception
     */
    public function testSetStorage(): void
    {
        $dto = (new FileContentDto('data', 'csv', 'name'));

        self::expectException(FileStorageException::class);
        $dto->setStorageType('private');
    }

    /**
     * @throws Exception
     */
    public function testSetFormat(): void
    {
        $dto = (new FileContentDto('data', 'csv', 'name'));

        self::expectException(FileStorageException::class);
        $dto->setFormat('png');
    }

}
