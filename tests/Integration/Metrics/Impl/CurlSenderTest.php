<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Metrics\Impl;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Hanaboso\CommonsBundle\Metrics\Impl\CurlSender;
use Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory;

/**
 * Class CurlSenderTest
 *
 * @package CommonsBundleTests\Integration\Metrics\Impl
 */
final class CurlSenderTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Metrics\Impl\CurlSender::send
     *
     * @throws Exception
     * @throws GuzzleException
     */
    public function testSend(): void
    {
        $client = self::createMock(Client::class);
        $client->method('send')->willReturn(new Response(200, [], ''));

        $factory = self::createMock(CurlClientFactory::class);
        $factory->method('create')->willReturn($client);

        $sender = new CurlSender($factory, 'https://test.com');

        self::assertTrue($sender->send(['asd' => '123'], ['a' => 'c']));
        self::assertTrue($sender->send(['asd' => 'qwe'], ['a' => 'b']));
    }

}
