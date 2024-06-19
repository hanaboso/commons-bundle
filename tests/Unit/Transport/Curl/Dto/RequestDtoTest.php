<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Curl\Dto;

use CommonsBundleTests\KernelTestCaseAbstract;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class RequestDtoTest
 *
 * @package CommonsBundleTests\Unit\Transport\Curl\Dto
 */
#[CoversClass(RequestDto::class)]
final class RequestDtoTest extends KernelTestCaseAbstract
{

    /**
     * @throws CurlException
     */
    public function testRequestDto(): void
    {
        $dto = new RequestDto(new Uri('www.example.com?id=5'), CurlManager::METHOD_POST, new ProcessDto());

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
     * @throws CurlException
     */
    public function testRequestDtoErr(): void
    {
        $dto = new RequestDto(new Uri('www.example.com?id=5'), CurlManager::METHOD_GET, new ProcessDto());

        self::expectException(CurlException::class);
        $dto->setBody('body');
    }

    /**
     * @throws CurlException
     */
    public function testRequestConstrErr(): void
    {
        self::expectException(CurlException::class);
        new RequestDto(new Uri('www.example.com?id=5'), 'aaaa', new ProcessDto());
    }

}
