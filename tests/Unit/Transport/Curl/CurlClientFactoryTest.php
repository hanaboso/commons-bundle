<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Curl;

use Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Class CurlClientFactoryTest
 *
 * @package CommonsBundleTests\Unit\Transport\Curl
 */
#[CoversClass(CurlClientFactory::class)]
final class CurlClientFactoryTest extends TestCase
{

    use CustomAssertTrait;

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $curlClientFactory = new CurlClientFactory();
        $curlClientFactory->create();

        self::assertFake();
    }

}
