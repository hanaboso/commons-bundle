<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Transport\Curl;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;

/**
 * Class CurlManagerTest
 *
 * @package CommonsBundleTests\Integration\Transport\Curl
 */
final class CurlManagerTest extends KernelTestCaseAbstract
{

    /**
     * @throws Exception
     */
    public function testSend(): void
    {
        $client = self::createMock(Client::class);
        $client->method('send')->willReturn(new Response(200, [], ''));

        $factory = self::createMock(CurlClientFactory::class);
        $factory->method('create')->willReturn($client);

        $curlManager = new CurlManager($factory);

        $requestDto = new RequestDto(CurlManager::METHOD_GET, new Uri('https://google.cz'));
        $requestDto->setHeaders(['Cache-Control' => 'private, max-age=0']);
        self::assertEquals(200, $curlManager->send($requestDto)->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testSendNotFound(): void
    {
        self::expectException(CurlException::class);
        self::expectExceptionCode(303);

        /** @var CurlManager $curlManager */
        $curlManager = self::getContainer()->get('hbpf.transport.curl_manager');

        $requestDto = new RequestDto(CurlManager::METHOD_GET, new Uri('some-unknown-address'));
        $curlManager->send($requestDto)->getStatusCode();
    }

}
