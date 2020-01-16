<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Curl\Dto;

use CommonsBundleTests\KernelTestCaseAbstract;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;

/**
 * Class RequestDtoTest
 *
 * @package CommonsBundleTests\Unit\Transport\Curl\Dto
 */
class RequestDtoTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto::getUriString
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto::getUri
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto::setUri
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto::setBody
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto::setDebugInfo
     *
     * @throws CurlException
     */
    public function testRequestDto(): void
    {
        $dto = new RequestDto(CurlManager::METHOD_POST, new Uri('www.example.com?id=5'));

        self::assertEquals('www.example.com?id=5', $dto->getUri(TRUE));
        self::assertEquals('www.example.com?id=5', $dto->getUriString());

        $dto->setUri(new Uri('www.example.cz?id=5'));
        self::assertEquals('www.example.cz?id=5', $dto->getUriString());

        $dto->setBody('body');
        self::assertEquals('body', $dto->getBody());

        $dto->setDebugInfo(new ProcessDto());
        self::assertEquals([], $dto->getDebugInfo());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto::setBody
     *
     * @throws CurlException
     */
    public function testRequestDtoErr(): void
    {
        $dto = new RequestDto(CurlManager::METHOD_GET, new Uri('www.example.com?id=5'));

        self::expectException(CurlException::class);
        $dto->setBody('body');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto
     *
     * @throws CurlException
     */
    public function testRequestConstrErr(): void
    {
        self::expectException(CurlException::class);
        new RequestDto('aaaa', new Uri('www.example.com?id=5'));
    }

}
