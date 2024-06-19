<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Curl\Dto;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\ResponseDto;
use Hanaboso\Utils\String\Json;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class ResponseDtoTest
 *
 * @package CommonsBundleTests\Unit\Transport\Curl\Dto
 */
#[CoversClass(ResponseDto::class)]
final class ResponseDtoTest extends KernelTestCaseAbstract
{

    /**
     * @throws Exception
     */
    public function testResponseDto(): void
    {
        $dto = new ResponseDto(205, 'created', Json::encode('body'), ['header' => 1]);

        self::assertEquals('created', $dto->getReasonPhrase());
        self::assertEquals('"body"', $dto->getBody());
        self::assertEquals([0 => 'body'], $dto->getJsonBody());
        self::assertEquals(['header' => 1], $dto->getHeaders());
    }

}
