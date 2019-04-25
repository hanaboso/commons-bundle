<?php declare(strict_types=1);

namespace Tests\Unit\Utils;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Enum\MetricsEnum;
use Hanaboso\CommonsBundle\Metrics\InfluxDbSender;
use Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\KernelTestCaseAbstract;

/**
 * Class CurlMetricsUtilsTest
 *
 * @package Tests\Unit\Utils
 */
final class CurlMetricsUtilsTest extends KernelTestCaseAbstract
{

    /**
     * @throws Exception
     */
    public function testCurlMetrics(): void
    {
        $influx = self::createMock(InfluxDbSender::class);
        $influx
            ->expects(self::any())
            ->method('send')->will($this->returnCallback(
                function (array $times, array $data): bool {
                    $data;

                    self::assertGreaterThan(0, $times[MetricsEnum::REQUEST_TOTAL_DURATION_SENT]);

                    return TRUE;
                }
            ));

        /** @var MockObject|Client $client */
        $client = self::createMock(Client::class);
        $client->method('send')->willReturn(new Response(200, [], ''));

        /** @var MockObject|CurlClientFactory $factory */
        $factory = self::createMock(CurlClientFactory::class);
        $factory->method('create')->willReturn($client);

        /** @var CurlManager $manager */
        $manager = new CurlManager($factory);
        $dto     = new RequestDto('GET', new Uri('http://google.com'));
        $manager->send($dto);
    }

}
