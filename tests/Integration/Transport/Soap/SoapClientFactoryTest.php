<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Transport\Soap;

use Exception;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\NonWsdl\RequestDto as RequestDtoNonWsdl;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\Wsdl\RequestDto as RequestDtoWsdl;
use Hanaboso\CommonsBundle\Transport\Soap\SoapClientFactory;
use Hanaboso\CommonsBundle\Transport\Soap\SoapException;
use PHPUnit\Framework\TestCase;
use SoapClient;

/**
 * Class SoapClientFactoryTest
 *
 * @package CommonsBundleTests\Integration\Transport\Soap
 */
final class SoapClientFactoryTest extends TestCase
{

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapClientFactory::create()
     *
     * @throws Exception
     */
    public function testCreateSoapClientWsdlFail(): void
    {
        $request = new RequestDtoWsdl('functionName', [], 'namespace', new Uri('abc'));
        $request->setVersion(SOAP_1_2);

        self::expectException(SoapException::class);
        self::expectExceptionCode(SoapException::INVALID_WSDL);

        $soapClientFactory = new SoapClientFactory();
        $soapClientFactory->create($request, ['uri' => '', 'location' => '']);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapClientFactory::create()
     *
     * @throws Exception
     */
    public function testCreateSoapClientNonWsdl(): void
    {
        $request = new RequestDtoNonWsdl('functionName', [], 'namespace', new Uri(''));
        $request->setVersion(SOAP_1_2);

        $soapClientFactory = new SoapClientFactory();
        $result            = $soapClientFactory->create($request, ['uri' => '', 'location' => '']);

        self::assertInstanceOf(SoapClient::class, $result);
    }

}
