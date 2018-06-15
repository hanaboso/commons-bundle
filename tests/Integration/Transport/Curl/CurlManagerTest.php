<?php declare(strict_types=1);

namespace Tests\Integration\Transport\Curl;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
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
        $this->curl = $this->c->get('hbpf.transport.curl_manager');
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function testSend(): void
    {
        $requestDto = (new RequestDto(CurlManager::METHOD_GET, new Uri('https://google.cz')))
            ->setHeaders(['Cache-Control' => 'private, max-age=0']);
        $this->assertEquals(200, $this->curl->send($requestDto)->getStatusCode());
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function testSendNotFound(): void
    {
        $this->expectException(CurlException::class);
        $this->expectExceptionCode(303);

        $requestDto = new RequestDto(CurlManager::METHOD_GET, new Uri('some-unknown-address'));
        $this->curl->send($requestDto)->getStatusCode();
    }

}