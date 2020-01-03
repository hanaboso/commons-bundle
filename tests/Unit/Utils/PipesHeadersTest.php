<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Utils;

use Hanaboso\CommonsBundle\Utils\PipesHeaders;
use PHPUnit\Framework\TestCase;

/**
 * Class PipesHeadersTest
 *
 * @package CommonsBundleTests\Unit\Utils
 */
final class PipesHeadersTest extends TestCase
{

    /**
     * @covers \Hanaboso\CommonsBundle\Utils\PipesHeaders::createKey()
     */
    public function testCreateKey(): void
    {
        self::assertSame('pf-node-id', PipesHeaders::createKey('node-id'));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Utils\PipesHeaders::clear()
     */
    public function testClear(): void
    {
        self::assertSame(
            ['content-type' => 'application/json', 'pf-token' => '456'],
            PipesHeaders::clear(
                [
                    'content-type' => 'application/json', 'pfp-node-id' => '123', 'pf-token' => '456',
                ]
            )
        );
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Utils\PipesHeaders::get()
     */
    public function testGet(): void
    {
        self::assertSame(
            '456',
            PipesHeaders::get(
                'token',
                [
                    'content-type' => 'application/json', 'pfp-node-id' => '123', 'pf-token' => '456',
                ]
            )
        );
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Utils\PipesHeaders::debugInfo()
     */
    public function testDebugInfo(): void
    {
        self::assertSame(
            [
                'node_id'        => '123',
                'correlation_id' => '456',
            ],
            PipesHeaders::debugInfo(
                [
                    'content-type'      => 'application/json',
                    'pf-node-id'        => '123',
                    'pf-token'          => '456',
                    'pf-correlation-id' => '456',
                ]
            )
        );
    }

}
