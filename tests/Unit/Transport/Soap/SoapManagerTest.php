<?php declare(strict_types=1);

namespace Tests\Unit\Transport\Soap;

use Exception;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Metrics\InfluxDbSender;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseDto;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseHeaderDto;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\Wsdl\RequestDto;
use Hanaboso\CommonsBundle\Transport\Soap\SoapClientFactory;
use Hanaboso\CommonsBundle\Transport\Soap\SoapManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SoapClient;

/**
 * Class SoapManagerTest
 *
 * @package Tests\Unit\Transport\Soap
 */
final class SoapManagerTest extends TestCase
{

    /**
     * @covers SoapManager::send()
     *
     * @throws Exception
     */
    public function testSend(): void
    {
        $soapCallResponse    = 'abc';
        $lastResponseHeaders = 'def';

        $client = $this->createPartialMock(SoapClient::class, ['__soapCall', '__getLastResponseHeaders']);
        $client->method('__soapCall')->willReturn($soapCallResponse);
        $client->method('__getLastResponseHeaders')->willReturn($lastResponseHeaders);

        /** @var MockObject|SoapClientFactory $soapClientFactory */
        $soapClientFactory = $this->createPartialMock(SoapClientFactory::class, ['create']);
        $soapClientFactory->method('create')->willReturn($client);

        $request = new RequestDto('', [], '', new Uri(''));
        $request->setVersion(SOAP_1_2);

        /** @var InfluxDbSender $influx */
        $influx = $this->createMock(InfluxDbSender::class);

        $soapManager = new SoapManager($soapClientFactory);
        $soapManager->setInfluxSender($influx);
        $result = $soapManager->send($request);

        $this->assertInstanceOf(ResponseDto::class, $result);
        $this->assertEquals($soapCallResponse, $result->getSoapCallResponse());
        $this->assertEquals($lastResponseHeaders, $result->getLastResponseHeaders());
        $this->assertInstanceOf(ResponseHeaderDto::class, $result->getResponseHeaderDto());
    }

}