<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Soap\Dto;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class ResponseDtoTest
 *
 * @package CommonsBundleTests\Unit\Transport\Soap\Dto
 */
#[CoversClass(ResponseDto::class)]
final class ResponseDtoTest extends KernelTestCaseAbstract
{

    /**
     * @return void
     */
    public function testResponseDto(): void
    {
        $dto = new ResponseDto('response', NULL, NULL);
        self::assertEquals('response', $dto->getSoapCallResponse());
        self::assertNull($dto->getLastResponseHeaders());
    }

}
