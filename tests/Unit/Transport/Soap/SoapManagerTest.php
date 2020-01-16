<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Soap;

use Exception;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Metrics\Impl\InfluxDbSender;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseDto;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\ResponseHeaderDto;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\Wsdl\RequestDto;
use Hanaboso\CommonsBundle\Transport\Soap\SoapClientFactory;
use Hanaboso\CommonsBundle\Transport\Soap\SoapException;
use Hanaboso\CommonsBundle\Transport\Soap\SoapManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SoapClient;

/**
 * Class SoapManagerTest
 *
 * @package CommonsBundleTests\Unit\Transport\Soap
 */
final class SoapManagerTest extends TestCase
{

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::send()
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::handleResponse()
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::getHeadersAsString()
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::sendMetrics()
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::setMetricsSender()
     *
     * @throws Exception
     */
    public function testSend(): void
    {
        $soapCallResponse    = 'abc';
        $lastResponseHeaders = 'def';

        /** @var MockObject|SoapClient $client */
        $client = self::createPartialMock(SoapClient::class, ['__soapCall', '__getLastResponseHeaders']);
        $client->expects(self::any())->method('__soapCall')->willReturn($soapCallResponse);
        $client->expects(self::any())->method('__getLastResponseHeaders')->willReturn($lastResponseHeaders);

        /** @var MockObject|SoapClientFactory $soapClientFactory */
        $soapClientFactory = self::createPartialMock(SoapClientFactory::class, ['create']);
        $soapClientFactory->expects(self::any())->method('create')->willReturn($client);

        $request = new RequestDto('', [], '', new Uri(''));
        $request->setVersion(SOAP_1_2);

        /** @var InfluxDbSender $influx */
        $influx      = self::createMock(InfluxDbSender::class);
        $loader      = new MetricsSenderLoader('influx', $influx, NULL);
        $soapManager = new SoapManager($soapClientFactory);
        $soapManager->setMetricsSender($loader);
        $result = $soapManager->send($request);

        self::assertInstanceOf(ResponseDto::class, $result);
        self::assertEquals($soapCallResponse, $result->getSoapCallResponse());
        self::assertEquals($lastResponseHeaders, $result->getLastResponseHeaders());
        self::assertInstanceOf(ResponseHeaderDto::class, $result->getResponseHeaderDto());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::send()
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::handleResponse()
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::getHeadersAsString()
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::composeOptions()
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::setMetricsSender()
     * @throws SoapException
     */
    public function testSendLastHeadersNull(): void
    {
        $client = self::createPartialMock(SoapClient::class, ['__soapCall', '__getLastResponseHeaders']);
        $client->expects(self::any())->method('__getLastResponseHeaders')->willReturn(NULL);

        /** @var MockObject|SoapClientFactory $soapClientFactory */
        $soapClientFactory = self::createPartialMock(SoapClientFactory::class, ['create']);
        $soapClientFactory->expects(self::any())->method('create')->willReturn($client);

        $request = new RequestDto('', [], 'namespace', new Uri(''), ['el1', 'el2']);
        $request->setVersion(SOAP_1_2);
        $request->setAuth('user', 'passwd');

        $soapManager = new SoapManager($soapClientFactory);
        $result      = $soapManager->send($request);

        self::assertInstanceOf(ResponseDto::class, $result);

        $request = new RequestDto('', [], 'namespace', new Uri(''), ['el1', 'el2']);
        $request->setVersion(SOAP_1_2);
        $request->setAuth('user', 'passwd');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::sendMetrics
     *
     * @throws SoapException
     */
    public function testSendSendMetrics(): void
    {
        $client = self::createPartialMock(SoapClient::class, ['__soapCall', '__getLastResponseHeaders']);
        $client->expects(self::any())->method('__getLastResponseHeaders')->willReturn(NULL);

        /** @var MockObject|SoapClientFactory $soapClientFactory */
        $soapClientFactory = self::createPartialMock(SoapClientFactory::class, ['create']);
        $soapClientFactory->expects(self::any())->method('create')->willReturn($client);

        $metricsSender = self::createPartialMock(MetricsSenderLoader::class, ['getSender']);
        $metricsSender->expects(self::any())->method('getSender')->willThrowException(new CurlException());

        $request = new RequestDto('', [], 'namespace', new Uri(''), ['el1', 'el2']);
        $request->setVersion(SOAP_1_2);
        $soapManager = new SoapManager($soapClientFactory);
        $soapManager->setMetricsSender($metricsSender);

        self::expectException(SoapException::class);
        $soapManager->send($request);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::send
     *
     * @throws SoapException
     */
    public function testSendErr(): void
    {
        /** @var MockObject|SoapClientFactory $soapClientFactory */
        $soapClientFactory = self::createPartialMock(SoapClientFactory::class, ['create']);
        $soapClientFactory->expects(self::any())->method('create')->willThrowException(new Exception());

        $request = new RequestDto('', [], '', new Uri(''));
        $request->setVersion(SOAP_1_2);

        self::expectException(Exception::class);
        (new SoapManager($soapClientFactory))->send($request);
    }

}
