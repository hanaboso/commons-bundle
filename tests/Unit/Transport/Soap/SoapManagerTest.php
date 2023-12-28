<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Soap;

use Exception;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Metrics\Impl\CurlSender;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\Wsdl\RequestDto;
use Hanaboso\CommonsBundle\Transport\Soap\SoapClientFactory;
use Hanaboso\CommonsBundle\Transport\Soap\SoapException;
use Hanaboso\CommonsBundle\Transport\Soap\SoapManager;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use PHPUnit\Framework\TestCase;
use SoapClient;

/**
 * Class SoapManagerTest
 *
 * @package CommonsBundleTests\Unit\Transport\Soap
 */
final class SoapManagerTest extends TestCase
{

    use CustomAssertTrait;

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::send
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::handleResponse
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::getHeadersAsString
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::sendMetrics
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::setMetricsSender
     *
     * @throws Exception
     */
    public function testSend(): void
    {
        $soapCallResponse    = 'abc';
        $lastResponseHeaders = 'def';

        $client = self::createPartialMock(SoapClient::class, ['__soapCall', '__getLastResponseHeaders']);
        $client->expects(self::any())->method('__soapCall')->willReturn($soapCallResponse);
        $client->expects(self::any())->method('__getLastResponseHeaders')->willReturn($lastResponseHeaders);

        $soapClientFactory = self::createPartialMock(SoapClientFactory::class, ['create']);
        $soapClientFactory->expects(self::any())->method('create')->willReturn($client);

        $request = new RequestDto('', [], '', new Uri(''));
        $request->setVersion(SOAP_1_2);

        $influx      = self::createMock(CurlSender::class);
        $loader      = new MetricsSenderLoader($influx);
        $soapManager = new SoapManager($soapClientFactory);
        $soapManager->setMetricsSender($loader);
        $result = $soapManager->send($request);

        self::assertEquals($soapCallResponse, $result->getSoapCallResponse());
        self::assertEquals($lastResponseHeaders, $result->getLastResponseHeaders());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::send
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::handleResponse
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::getHeadersAsString
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::composeOptions
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::setMetricsSender
     * @throws SoapException
     */
    public function testSendLastHeadersNull(): void
    {
        $client = self::createPartialMock(SoapClient::class, ['__soapCall', '__getLastResponseHeaders']);
        $client->expects(self::any())->method('__getLastResponseHeaders')->willReturn(NULL);

        $soapClientFactory = self::createPartialMock(SoapClientFactory::class, ['create']);
        $soapClientFactory->expects(self::any())->method('create')->willReturn($client);

        $request = new RequestDto('', [], 'namespace', new Uri(''), ['el1', 'el2']);
        $request->setVersion(SOAP_1_2);
        $request->setAuth('user', 'passwd');

        $soapManager = new SoapManager($soapClientFactory);
        $soapManager->send($request);

        $request = new RequestDto('', [], 'namespace', new Uri(''), ['el1', 'el2']);
        $request->setVersion(SOAP_1_2);
        $request->setAuth('user', 'passwd');
        self::assertFake();
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
        $soapClientFactory = self::createPartialMock(SoapClientFactory::class, ['create']);
        $soapClientFactory->expects(self::any())->method('create')->willThrowException(new Exception());

        $request = new RequestDto('', [], '', new Uri(''));
        $request->setVersion(SOAP_1_2);

        self::expectException(Exception::class);
        (new SoapManager($soapClientFactory))->send($request);
    }

}
