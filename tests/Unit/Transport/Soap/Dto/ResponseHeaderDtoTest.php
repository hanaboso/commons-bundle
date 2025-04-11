<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Soap\Dto;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseHeaderDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class ResponseHeaderDtoTest
 *
 * @package CommonsBundleTests\Unit\Transport\Soap\Dto
 */
#[CoversClass(ResponseHeaderDto::class)]
final class ResponseHeaderDtoTest extends KernelTestCaseAbstract
{

    /**
     * @return void
     */
    public function testResponseHeaderDto(): void
    {
        $dto = new ResponseHeaderDto(NULL, 'v2', 202, 'done');

        self::assertNull($dto->getHttpHeaders());
        self::assertSame('v2', $dto->getHttpVersion());
        self::assertSame(202, $dto->getHttpStatusCode());
        self::assertSame('done', $dto->getHttpReason());
    }

}
