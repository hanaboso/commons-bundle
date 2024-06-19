<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Transport\Soap;

use Exception;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\NonWsdl\RequestDto as RequestDtoNonWsdl;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\Wsdl\RequestDto as RequestDtoWsdl;
use Hanaboso\CommonsBundle\Transport\Soap\SoapClientFactory;
use Hanaboso\CommonsBundle\Transport\Soap\SoapException;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Class SoapClientFactoryTest
 *
 * @package CommonsBundleTests\Integration\Transport\Soap
 */
#[CoversClass(SoapClientFactory::class)]
final class SoapClientFactoryTest extends TestCase
{

    use CustomAssertTrait;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @throws Exception
     */
    public function testCreateSoapClientWsdlFail(): void
    {
        $request = new RequestDtoWsdl('functionName', [], 'namespace', new Uri('https://example.com'));
        $request->setVersion(SOAP_1_2);

        self::expectException(SoapException::class);
        self::expectExceptionCode(SoapException::INVALID_WSDL);

        $soapClientFactory = new SoapClientFactory();
        $soapClientFactory->create($request, ['uri' => '', 'location' => '']);
    }

    /**
     * @throws Exception
     */
    public function testCreateSoapClientNonWsdl(): void
    {
        $request = new RequestDtoNonWsdl('functionName', [], 'namespace', new Uri(''));
        $request->setVersion(SOAP_1_2);

        $soapClientFactory = new SoapClientFactory();
        $soapClientFactory->create($request, ['uri' => '', 'location' => '']);

        self::assertFake();
    }

}
