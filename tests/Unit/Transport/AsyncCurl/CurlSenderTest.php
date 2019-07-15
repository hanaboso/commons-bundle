<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: venca
 * Date: 10.10.17
 * Time: 13:52
 */

namespace Tests\Unit\Transport\AsyncCurl;

use Clue\React\Buzz\Browser;
use Clue\React\Buzz\Message\ResponseException;
use Exception;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Metrics\Impl\InfluxDbSender;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
use Hanaboso\CommonsBundle\Transport\AsyncCurl\CurlSender;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use function React\Promise\reject;
use function React\Promise\resolve;

/**
 * Class CurlSenderTest
 *
 * @package Tests\Unit\Transport\AsyncCurl
 */
final class CurlSenderTest extends TestCase
{

    /**
     * @covers CurlSender::send()
     * @throws Exception
     */
    public function testSend(): void
    {
        /** @var Browser|MockObject $browser */
        $browser = self::createMock(Browser::class);
        $browser->expects(self::any())->method('send')->willReturn(resolve(new Response(201)));

        /** @var InfluxDbSender $influx */
        $influx = self::createMock(InfluxDbSender::class);
        $loader = new MetricsSenderLoader('influx', $influx, NULL);

        $curl = new CurlSender($browser);
        $curl->setMetricsSender($loader);
        $request = new RequestDto('GET', new Uri('https://cleverconn.stage.hanaboso.net/api/'));

        $curl
            ->send($request)
            ->then(function (ResponseInterface $response): void {
                self::assertSame(201, $response->getStatusCode());
            })
            ->done();
    }

    /**
     * @covers CurlSender::send()
     * @throws Exception
     */
    public function testSendException(): void
    {
        /** @var Browser|MockObject $browser */
        $browser = self::createMock(Browser::class);
        $browser->expects(self::any())->method('send')->willReturn(reject(new ResponseException(new Response(401))));

        $curl    = new CurlSender($browser);
        $request = new RequestDto('GET', new Uri('https://cleverconn.stage.hanaboso.net/api/'));

        $curl
            ->send($request)
            ->then(NULL, function ($e): void {
                self::assertInstanceOf(ResponseException::class, $e);
            })
            ->done();
    }

}
