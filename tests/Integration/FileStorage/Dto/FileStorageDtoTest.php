<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Dto;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Hanaboso\CommonsBundle\FileStorage\Document\File;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileStorageDto;

/**
 * Class FileStorageDtoTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Dto
 */
final class FileStorageDtoTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Dto\FileStorageDto::getContent
     * @covers \Hanaboso\CommonsBundle\FileStorage\Dto\FileStorageDto::getFile
     * @covers \Hanaboso\CommonsBundle\FileStorage\Dto\FileStorageDto
     */
    public function testFileStorageDto(): void
    {
        $dto = new FileStorageDto(new File(), 'data');

        self::assertInstanceOf(File::class, $dto->getFile());
        self::assertEquals('data', $dto->getContent());
    }

}
