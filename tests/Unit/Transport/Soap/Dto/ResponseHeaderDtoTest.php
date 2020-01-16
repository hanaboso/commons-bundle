<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Soap\Dto;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseHeaderDto;

/**
 * Class ResponseHeaderDtoTest
 *
 * @package CommonsBundleTests\Unit\Transport\Soap\Dto
 */
final class ResponseHeaderDtoTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseHeaderDto
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseHeaderDto::getHttpHeaders
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseHeaderDto::getHttpVersion
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseHeaderDto::getHttpStatusCode
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseHeaderDto::getHttpReason
     */
    public function testResponseHeaderDto(): void
    {
        $dto = new ResponseHeaderDto(NULL, 'v2', 202, 'done');

        self::assertNull($dto->getHttpHeaders());
        self::assertEquals('v2', $dto->getHttpVersion());
        self::assertEquals(202, $dto->getHttpStatusCode());
        self::assertEquals('done', $dto->getHttpReason());
    }

}
