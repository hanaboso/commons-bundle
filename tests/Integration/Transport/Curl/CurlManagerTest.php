<?php declare(strict_types=1);

namespace Tests\Integration\Transport\Curl;

use Exception;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;
use Tests\KernelTestCaseAbstract;

/**
 * Class CurlManagerTest
 *
 * @package Tests\Integration\Transport\Curl
 */
final class CurlManagerTest extends KernelTestCaseAbstract
{

    /**
     * @var CurlManager
     */
    private $curl;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        /** @var CurlManager $curlManager */
        $curlManager = self::$container->get('hbpf.transport.curl_manager');
        $this->curl = $curlManager;
    }

    /**
     * @throws Exception
     */
    public function testSend(): void
    {
        $requestDto = (new RequestDto(CurlManager::METHOD_GET, new Uri('https://google.cz')))
            ->setHeaders(['Cache-Control' => 'private, max-age=0']);
        $this->assertEquals(200, $this->curl->send($requestDto)->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testSendNotFound(): void
    {
        $this->expectException(CurlException::class);
        $this->expectExceptionCode(303);

        $requestDto = new RequestDto(CurlManager::METHOD_GET, new Uri('some-unknown-address'));
        $this->curl->send($requestDto)->getStatusCode();
    }

}