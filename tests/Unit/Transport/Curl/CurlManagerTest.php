<?php declare(strict_types=1);

namespace Tests\Unit\Transport\Curl;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Metrics\Impl\InfluxDbSender;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
use Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\ResponseDto;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class CurlManagerTest
 *
 * @package Tests\Unit\Transport\Curl
 */
final class CurlManagerTest extends TestCase
{

    /**
     * @covers CurlManager::send()
     *
     * @throws Exception
     */
    public function testSend(): void
    {
        $body    = (string) json_encode(['abc' => 'def'], JSON_THROW_ON_ERROR);
        $headers = ['header_key' => 'header_value'];

        $psr7Response = new Response(200, $headers, $body);

        /** @var MockObject|Client $client */
        $client = self::createPartialMock(Client::class, ['send']);
        $client->expects(self::any())->method('send')->willReturn($psr7Response);

        /** @var MockObject|CurlClientFactory $curlClientFactory */
        $curlClientFactory = self::createPartialMock(CurlClientFactory::class, ['create']);
        $curlClientFactory->expects(self::any())->method('create')->willReturn($client);

        $requestDto = new RequestDto(CurlManager::METHOD_GET, new Uri('http://example.com'));

        /** @var InfluxDbSender $influx */
        $influx = self::createMock(InfluxDbSender::class);

        $loader = new MetricsSenderLoader('influx', $influx, NULL);

        $curlManager = new CurlManager($curlClientFactory);
        $curlManager->setMetricsSender($loader);
        $result = $curlManager->send($requestDto);

        self::assertInstanceOf(ResponseDto::class, $result);
        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals('OK', $result->getReasonPhrase());
        self::assertEquals(['header_key' => ['header_value']], $result->getHeaders());
        self::assertEquals($body, $result->getBody());
        self::assertEquals(['abc' => 'def'], $result->getJsonBody());
    }

    /**
     * @covers CurlManager::send()
     * @throws Exception
     */
    public function testSendFail(): void
    {
        self::expectException(CurlException::class);
        $requestDto = new RequestDto(CurlManager::METHOD_GET, new Uri('http://example.com'));

        /** @var InfluxDbSender $influx */
        $influx = self::createMock(InfluxDbSender::class);
        $loader = new MetricsSenderLoader('influx', $influx, NULL);

        $curlManager = new CurlManager(new CurlClientFactory());
        $curlManager->setMetricsSender($loader);
        $curlManager->send($requestDto, ['headers' => 123]);
    }

    /**
     * @covers CurlManager::send()
     * @throws Exception
     */
    public function testSendFailMethod(): void
    {
        self::expectException(CurlException::class);
        self::expectExceptionCode(CurlException::INVALID_METHOD);
        new RequestDto('nonsense', new Uri('http://example.com'));
    }

    /**
     * @covers CurlManager::send()
     * @throws Exception
     */
    public function testSendFailBody(): void
    {
        self::expectException(CurlException::class);
        self::expectExceptionCode(CurlException::BODY_ON_GET);
        $requestDto = new RequestDto(CurlManager::METHOD_GET, new Uri('http://example.com'));
        $requestDto->setBody('');
    }

}
