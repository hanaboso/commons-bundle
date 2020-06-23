<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Curl\Dto;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\ResponseDto;
use Hanaboso\Utils\String\Json;

/**
 * Class ResponseDtoTest
 *
 * @package CommonsBundleTests\Unit\Transport\Curl\Dto
 */
class ResponseDtoTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\Dto\ResponseDto::getReasonPhrase
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\Dto\ResponseDto::getBody
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\Dto\ResponseDto::getJsonBody
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\Dto\ResponseDto::getHeaders
     *
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
