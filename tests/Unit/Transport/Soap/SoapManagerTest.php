<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Soap;

use Exception;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Metrics\Impl\InfluxDbSender;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
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
 * @package CommonsBundleTests\Unit\Transport\Soap
 */
final class SoapManagerTest extends TestCase
{

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapManager::send()
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

}
