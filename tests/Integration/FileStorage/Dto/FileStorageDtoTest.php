<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Dto;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Hanaboso\CommonsBundle\FileStorage\Document\File;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileStorageDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class FileStorageDtoTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Dto
 */
#[CoversClass(FileStorageDto::class)]
final class FileStorageDtoTest extends DatabaseTestCaseAbstract
{

    /**
     * @return void
     */
    public function testFileStorageDto(): void
    {
        $dto = new FileStorageDto(new File(), 'data');

        self::assertInstanceOf(File::class, $dto->getFile());
        self::assertEquals('data', $dto->getContent());
    }

}
