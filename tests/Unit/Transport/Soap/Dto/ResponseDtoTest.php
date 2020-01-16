<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Soap\Dto;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseDto;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseHeaderDto;

/**
 * Class ResponseDtoTest
 *
 * @package CommonsBundleTests\Unit\Transport\Soap\Dto
 */
final class ResponseDtoTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseDto
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseDto::getSoapCallResponse
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseDto::getResponseHeaderDto
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseDto::getLastResponseHeaders
     */
    public function testResponseDto(): void
    {
        $dto = new ResponseDto('response', NULL, NULL);
        self::assertEquals('response', $dto->getSoapCallResponse());
        self::assertNull($dto->getLastResponseHeaders());
        self::assertInstanceOf(ResponseHeaderDto::class, $dto->getResponseHeaderDto());
    }

}
