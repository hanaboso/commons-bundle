<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Curl;

use Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class CurlClientFactoryTest
 *
 * @package CommonsBundleTests\Unit\Transport\Curl
 */
final class CurlClientFactoryTest extends TestCase
{

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory::create()
     */
    public function testCreate(): void
    {
        $curlClientFactory = new CurlClientFactory();
        $curlClientFactory->create();

        self::assertEmpty([]);
    }

}
