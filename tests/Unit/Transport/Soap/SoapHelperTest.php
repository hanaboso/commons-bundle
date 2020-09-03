<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Soap;

use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\NonWsdl\RequestDto as RequestDtoNonWsdl;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\Wsdl\RequestDto as RequestDtoWsdl;
use Hanaboso\CommonsBundle\Transport\Soap\SoapHelper;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SoapParam;
use SoapVar;
use stdClass;
use Symfony\Component\HttpFoundation\HeaderBag;

/**
 * Class SoapHelperTest
 *
 * @package CommonsBundleTests\Unit\Transport\Soap
 */
final class SoapHelperTest extends TestCase
{

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapHelper::composeRequestHeaders()
     */
    public function testComposeRequestHeaders(): void
    {
        $request = new RequestDtoNonWsdl('functionName', ['arguments'], 'namespace', new Uri(''));
        $result  = SoapHelper::composeRequestHeaders($request);
        self::assertEmpty($result);

        $request = new RequestDtoNonWsdl('functionName', ['arguments'], 'namespace', new Uri(''), ['el1', 'el2']);
        $result  = SoapHelper::composeRequestHeaders($request);

        self::assertNotEmpty($result);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapHelper::composeArguments
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapHelper::composeArgumentsForNonWsdl
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapHelper::composeDataForSoapParam
     */
    public function testComposeArguments(): void
    {
        $request = new RequestDtoNonWsdl('functionName', [], 'namespace', new Uri(''));
        $result  = SoapHelper::composeArguments($request);

        self::assertNull($result);

        $request = new RequestDtoNonWsdl('functionName', ['arguments'], 'namespace', new Uri(''));
        $result  = SoapHelper::composeArguments($request);

        self::assertIsArray($result);

        $request = new RequestDtoNonWsdl('functionName', ['arg1' => ['el1' => '1']], 'namespace', new Uri(''));
        $result  = SoapHelper::composeArguments($request);

        self::assertIsArray($result);

        $request = new RequestDtoNonWsdl(
            'functionName',
            ['arg1' => ['el1' => new StdClass()]],
            'namespace',
            new Uri('')
        );
        self::expectException(InvalidArgumentException::class);
        SoapHelper::composeArguments($request);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapHelper::composeArguments()
     */
    public function testComposeArgumentsWsdl(): void
    {
        $request = new RequestDtoWsdl('functionName', ['arguments'], 'namespace', new Uri(''));
        $result  = SoapHelper::composeArguments($request);

        self::assertEquals($result, $request->getArguments());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapHelper::composeArguments()
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
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapHelper::composeArguments()
     */
    public function testComposeArgumentsNonWsdlNull(): void
    {
        $request = new RequestDtoNonWsdl('functionName', [], 'namespace', new Uri(''));
        $result  = SoapHelper::composeArguments($request);

        self::assertNull($result);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapHelper::parseResponseHeaders()
     */
    public function testParseResponseHeaders(): void
    {
        $headers = 'HTTP/1.1 200 OK
Content-Type: text/xml; charset="utf-8"
Content-Length: nnnn';
        $result  = SoapHelper::parseResponseHeaders($headers);

        self::assertNotEmpty($result);
        self::assertArrayHasKey('version', $result);
        self::assertArrayHasKey('statusCode', $result);
        self::assertArrayHasKey('reason', $result);
        self::assertArrayHasKey('headers', $result);

        self::assertEquals('1.1', $result['version']);
        self::assertEquals(200, $result['statusCode']);
        self::assertEquals('OK', $result['reason']);

        $headerBag = $result['headers'];
        self::assertInstanceOf(HeaderBag::class, $headerBag);

        $expectedValues = [
            'content-type'   => ['text/xml; charset="utf-8"'],
            'content-length' => ['nnnn'],
        ];
        self::assertEquals($expectedValues, $headerBag->all());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\SoapHelper::parseResponseHeaders()
     */
    public function testParseResponseHeadersEmpty(): void
    {
        $result = SoapHelper::parseResponseHeaders(NULL);

        self::assertNotEmpty($result);
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
