<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Monolog;

use CommonsBundleTests\KernelTestCaseAbstract;
use DateTimeImmutable;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Hanaboso\CommonsBundle\Monolog\HttpHandler;
use Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory;
use Hanaboso\CommonsBundle\WorkerApi\Client as WorkerApiClient;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use Monolog\Level;
use Monolog\LogRecord;

/**
 * Class HttpHandlerTest
 *
 * @package CommonsBundleTests\Unit\Monolog
 */
final class HttpHandlerTest extends KernelTestCaseAbstract
{

    use PrivateTrait;
    use CustomAssertTrait;

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\HttpHandler
     * @covers \Hanaboso\CommonsBundle\Monolog\HttpHandler::write
     *
     * @throws Exception
     */
    public function testHttpHandler(): void
    {
        $client = self::createMock(Client::class);
        $client->method('send')->willReturn(new Response(200, [], ''));

        $factory = self::createMock(CurlClientFactory::class);
        $factory->method('create')->willReturn($client);

        $workerApi = new WorkerApiClient($factory, 'https://test.com', 'OrchestyApiKey');

        $handler = new HttpHandler($workerApi);

        $this->invokeMethod(
            $handler,
            'write',
            [new LogRecord(new DateTimeImmutable(), 'test', Level::Info, 'testMessage', ['is_for_ui' => TRUE])],
        );
        self::assertFake();
    }

}
