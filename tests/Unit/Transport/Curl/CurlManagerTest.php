<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Curl;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Metrics\Impl\InfluxDbSender;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;
use Hanaboso\Utils\String\Json;
use Hanaboso\Utils\System\PipesHeaders;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * Class CurlManagerTest
 *
 * @package CommonsBundleTests\Unit\Transport\Curl
 */
final class CurlManagerTest extends TestCase
{

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\CurlManager::send()
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\CurlManager::setTimeout()
     *
     * @throws Exception
     */
    public function testSend(): void
    {
        $body    = Json::encode(['abc' => 'def']);
        $headers = ['header_key' => 'header_value'];

        $psr7Response = new Response(200, $headers, $body);

        $client = self::createPartialMock(Client::class, ['send']);
        $client->expects(self::any())->method('send')->willReturn($psr7Response);

        $curlClientFactory = self::createPartialMock(CurlClientFactory::class, ['create']);
        $curlClientFactory->expects(self::any())->method('create')->willReturn($client);

        $requestDto = new RequestDto(CurlManager::METHOD_GET, new Uri('http://example.com'));

        /** @var InfluxDbSender $influx */
        $influx = self::createMock(InfluxDbSender::class);

        $loader = new MetricsSenderLoader('influx', $influx, NULL);

        $curlManager = new CurlManager($curlClientFactory);
        $curlManager->setMetricsSender($loader);
        $curlManager->setTimeout(5);
        $result = $curlManager->send($requestDto);

        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals('OK', $result->getReasonPhrase());
        self::assertEquals(['header_key' => ['header_value']], $result->getHeaders());
        self::assertEquals($body, $result->getBody());
        self::assertEquals(['abc' => 'def'], $result->getJsonBody());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\CurlManager::send()
     *
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
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\CurlManager::send()
     *
     * @throws Exception
     */
    public function testSendFailMethod(): void
    {
        self::expectException(CurlException::class);
        self::expectExceptionCode(CurlException::INVALID_METHOD);
        new RequestDto('nonsense', new Uri('http://example.com'));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\CurlManager::send()
     *
     * @throws Exception
     */
    public function testSendFailBody(): void
    {
        self::expectException(CurlException::class);
        self::expectExceptionCode(CurlException::BODY_ON_GET);
        $requestDto = new RequestDto(CurlManager::METHOD_GET, new Uri('http://example.com'));
        $requestDto->setBody('');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto::from()
     *
     * @throws Exception
     */
    public function testFrom(): void
    {
        $processDto = new ProcessDto();
        $processDto->setHeaders(
            [
                PipesHeaders::createKey(PipesHeaders::NODE_ID)        => '123',
                PipesHeaders::createKey(PipesHeaders::CORRELATION_ID) => 'aaa222',
            ]
        );

        $requestDto = new RequestDto(CurlManager::METHOD_GET, new Uri('http://example.com'));
        $requestDto->setDebugInfo($processDto);

        $res = RequestDto::from($requestDto, new Uri('www.google.com'), CurlManager::METHOD_POST);

        self::assertEquals('www.google.com', $res->getUriString());
        self::assertEquals(CurlManager::METHOD_POST, $res->getMethod());
        self::assertEquals(['node_id' => '123', 'correlation_id' => 'aaa222'], $res->getDebugInfo());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\CurlException::getResponse
     */
    public function testCurlException(): void
    {
        $exception = new CurlException('Ups, something went wrong', 400, NULL, new Response());

        self::assertInstanceOf(ResponseInterface::class, $exception->getResponse());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\CurlManager::send
     *
     * @throws CurlException
     */
    public function testSendErr(): void
    {
        $factory = self::createMock(CurlClientFactory::class);
        $factory->expects(self::any())->method('create')
            ->willThrowException(
                new RequestException(
                    'Ups, something went wrong',
                    new Request('method', ''),
                    new Response()
                )
            );

        $manager = new CurlManager($factory);
        $dto     = new RequestDto('GET', new Uri('http://google.com'));

        self::expectException(CurlException::class);
        $manager->send($dto);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\CurlManager::sendAsync
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\CurlManager::logResponse
     *
     * @throws CurlException
     */
    public function testSendAsync(): void
    {
        $promise = new FulfilledPromise(new Response(202, ['Accept-Language' => 'en', 'Accept' => 'text/html']));

        $client = self::createMock(Client::class);
        $client->method('sendAsync')->willReturn($promise);

        $factory = self::createMock(CurlClientFactory::class);
        $factory->method('create')->willReturn($client);

        $manager = new CurlManager($factory);
        $dto     = new RequestDto('GET', new Uri('http://google.com'));

        /** @var Response $response */
        $response = $manager->sendAsync($dto)->wait();
        self::assertEquals(202, $response->getStatusCode());
        self::assertArrayHasKey('Accept-Language', $response->getHeaders());
        self::assertArrayHasKey('Accept', $response->getHeaders());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\CurlManager::sendAsync
     * @throws CurlException
     */
    public function testSendAsyncException(): void
    {
        /** @var Promise $promise */
        $promise = new RejectedPromise(
            new ServerException('Ups, something with server went wrong.', new Request('message', 'uri'), new Response())
        );

        $client = self::createMock(Client::class);
        $client->method('sendAsync')->willReturn($promise);

        $factory = self::createMock(CurlClientFactory::class);
        $factory->method('create')->willReturn($client);

        $manager = new CurlManager($factory);
        $dto     = new RequestDto('GET', new Uri('http://google.com'));

        self::expectException(RequestException::class);
        $manager->sendAsync($dto)->wait();
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\CurlManager::sendAsync
     *
     * @throws CurlException
     */
    public function testSendAsyncReject(): void
    {
        /** @var Promise $promise */
        $promise = new RejectedPromise(
            new Exception('Ups, something went wrong.')
        );

        $client = self::createMock(Client::class);
        $client->method('sendAsync')->willReturn($promise);

        $factory = self::createMock(CurlClientFactory::class);
        $factory->method('create')->willReturn($client);

        $manager = new CurlManager($factory);
        $dto     = new RequestDto('GET', new Uri('http://google.com'));

        self::expectException(Exception::class);
        $manager->sendAsync($dto)->wait();
    }

}
