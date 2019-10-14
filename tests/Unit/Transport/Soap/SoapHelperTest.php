<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Soap;

use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\NonWsdl\RequestDto as RequestDtoNonWsdl;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\Wsdl\RequestDto as RequestDtoWsdl;
use Hanaboso\CommonsBundle\Transport\Soap\SoapHelper;
use PHPUnit\Framework\TestCase;
use SoapParam;
use SoapVar;
use Symfony\Component\HttpFoundation\HeaderBag;

/**
 * Class SoapHelperTest
 *
 * @package CommonsBundleTests\Unit\Transport\Soap
 */
final class SoapHelperTest extends TestCase
{

    /**
     * @covers SoapHelper::composeRequestHeaders()
     */
    public function testComposeRequestHeaders(): void
    {
        $request = new RequestDtoNonWsdl('functionName', ['arguments'], 'namespace', new Uri(''));
        $result  = SoapHelper::composeRequestHeaders($request);

        self::assertNull($result);
    }

    /**
     * @covers SoapHelper::composeArguments()
     */
    public function testComposeArgumentsWsdl(): void
    {
        $request = new RequestDtoWsdl('functionName', ['arguments'], 'namespace', new Uri(''));
        $result  = SoapHelper::composeArguments($request);

        self::assertEquals($result, $request->getArguments());
    }

    /**
     * @covers SoapHelper::composeArguments()
     */
    public function testComposeArgumentsNonWsdl(): void
    {
        $request = new RequestDtoNonWsdl('functionName', ['key1' => 'value1'], 'namespace', new Uri(''));
        $result  = SoapHelper::composeArguments($request);

        $soapVar   = new SoapVar('value1', XSD_STRING, '', '', 'ns1:key1');
        $soapParam = new SoapParam($soapVar, 'key1');
        self::assertEquals([$soapParam], $result);
    }

    /**
     * @covers SoapHelper::composeArguments()
     */
    public function testComposeArgumentsNonWsdlNull(): void
    {
        $request = new RequestDtoNonWsdl('functionName', [], 'namespace', new Uri(''));
        $result  = SoapHelper::composeArguments($request);

        self::assertNull($result);
    }

    /**
     * @covers SoapHelper::parseResponseHeaders()
     */
    public function testParseResponseHeaders(): void
    {
        $headers = 'HTTP/1.1 200 OK
Content-Type: text/xml; charset="utf-8"
Content-Length: nnnn';
        $result  = SoapHelper::parseResponseHeaders($headers);

        self::assertTrue(is_array($result));
        self::assertArrayHasKey('version', $result);
        self::assertArrayHasKey('statusCode', $result);
        self::assertArrayHasKey('reason', $result);
        self::assertArrayHasKey('headers', $result);

        self::assertEquals('1.1', $result['version']);
        self::assertEquals(200, $result['statusCode']);
        self::assertEquals('OK', $result['reason']);

        /** @var HeaderBag $headerBag */
        $headerBag = $result['headers'];
        self::assertInstanceOf(HeaderBag::class, $headerBag);

        $expectedValues = [
            'content-type'   => ['text/xml; charset="utf-8"'],
            'content-length' => ['nnnn'],
        ];
        self::assertEquals($expectedValues, $headerBag->all());
    }

    /**
     * @covers SoapHelper::parseResponseHeaders()
     */
    public function testParseResponseHeadersEmpty(): void
    {
        $result = SoapHelper::parseResponseHeaders(NULL);

        self::assertTrue(is_array($result));
        self::assertArrayHasKey('version', $result);
        self::assertArrayHasKey('statusCode', $result);
        self::assertArrayHasKey('reason', $result);
        self::assertArrayHasKey('headers', $result);

        self::assertNull($result['version']);
        self::assertNull($result['statusCode']);
        self::assertNull($result['reason']);
        self::assertNull($result['headers']);
    }

}
