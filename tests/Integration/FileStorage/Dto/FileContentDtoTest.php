<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Dto;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto;
use Hanaboso\Utils\Exception\EnumException;

/**
 * Class FileContentDtoTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Dto
 */
final class FileContentDtoTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto
     * @covers \Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto::getFilename
     * @covers \Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto::setFilename
     * @covers \Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto::getStorageType
     * @covers \Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto::setStorageType
     * @covers \Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto::getContent
     * @covers \Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto::setContent
     * @covers \Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto::getFormat
     * @covers \Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto::setFormat
     *
     * @throws FileStorageException
     * @throws EnumException
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
     * @covers \Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto::setStorageType
     *
     * @throws EnumException
     * @throws FileStorageException
     */
    public function testSetStorage(): void
    {
        $dto = (new FileContentDto('data', 'csv', 'name'));

        self::expectException(FileStorageException::class);
        $dto->setStorageType('private');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto::setFormat
     *
     * @throws EnumException
     * @throws FileStorageException
     */
    public function testSetFormat(): void
    {
        $dto = (new FileContentDto('data', 'csv', 'name'));

        self::expectException(FileStorageException::class);
        $dto->setFormat('png');
    }

}
