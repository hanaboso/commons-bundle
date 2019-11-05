<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Utils;

use Hanaboso\CommonsBundle\Transport\Utils\TransportFormatter;
use PHPUnit\Framework\TestCase;

/**
 * Class TransformFormatterTest
 *
 * @package CommonsBundleTests\Unit\Transport\Utils
 */
final class TransformFormatterTest extends TestCase
{

    /**
     * @covers TransportFormatter::headersToString()
     */
    public function testHeadersToString(): void
    {
        self::assertSame(
            'content-type=[application/json, application/js], pf_token=123',
            TransportFormatter::headersToString(
                [
                    'content-type' => ['application/json', 'application/js'], 'pf_token' => '123',
                ]
            )
        );
    }

    /**
     * @covers TransportFormatter::requestToString()
     */
    public function testRequestToString(): void
    {
        self::assertSame(
            'Request: Method: GET, Uri: http://localhost, Headers: content-type=application/json, Body: "{"data":[]}"',
            TransportFormatter::requestToString(
                'get',
                'http://localhost',
                ['content-type' => 'application/json'],
                '{"data":[]}'
            )
        );
    }

    /**
     * @covers TransportFormatter::responseToString()
     */
    public function testResponseToString(): void
    {
        self::assertSame(
            'Response: Status Code: 400, Reason Phrase: Bad Request, Headers: content-type=application/json, Body: "{"data":[]}"',
            TransportFormatter::responseToString(
                400,
                'Bad Request',
                ['content-type' => 'application/json'],
                '{"data":[]}'
            )
        );
    }

}
