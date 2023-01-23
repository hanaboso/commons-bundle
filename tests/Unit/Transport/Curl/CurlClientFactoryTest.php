<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Curl;

use Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class CurlClientFactoryTest
 *
 * @package CommonsBundleTests\Unit\Transport\Curl
 */
final class CurlClientFactoryTest extends TestCase
{

    use CustomAssertTrait;

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory::create()
     */
    public function testCreate(): void
    {
        $curlClientFactory = new CurlClientFactory();
        $curlClientFactory->create();

        self::assertFake();
    }

}
