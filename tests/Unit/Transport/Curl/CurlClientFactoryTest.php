<?php declare(strict_types=1);

namespace Tests\Unit\Transport\Curl;

use GuzzleHttp\Client;
use Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class CurlClientFactoryTest
 *
 * @package Tests\Unit\Transport\Curl
 */
final class CurlClientFactoryTest extends TestCase
{

    /**
     * @covers CurlClientFactory::create()
     */
    public function testCreate(): void
    {
        $curlClientFactory = new CurlClientFactory();
        $result            = $curlClientFactory->create();

        self::assertInstanceOf(Client::class, $result);
    }

}
