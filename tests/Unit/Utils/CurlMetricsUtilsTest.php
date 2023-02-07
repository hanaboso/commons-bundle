<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Utils;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Enum\MetricsEnum;
use Hanaboso\CommonsBundle\Metrics\Impl\CurlSender;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\CommonsBundle\Transport\Curl\CurlClientFactory;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;
use Hanaboso\CommonsBundle\Utils\CurlMetricUtils;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;

/**
 * Class CurlMetricsUtilsTest
 *
 * @package CommonsBundleTests\Unit\Utils
 */
final class CurlMetricsUtilsTest extends KernelTestCaseAbstract
{

    use CustomAssertTrait;

    /**
     * @throws Exception
     */
    public function testCurlMetrics(): void
    {
        $curlSender = self::createMock(CurlSender::class);
        $curlSender
            ->expects(self::any())
            ->method('send')->will(
                self::returnCallback(
                    static function (array $times, array $data): bool {
                        $data;

                        self::assertGreaterThanOrEqual(0, $times[MetricsEnum::REQUEST_TOTAL_DURATION_SENT->value]);

                        return TRUE;
                    },
                ),
            );

        $loader = new MetricsSenderLoader($curlSender);

        $client = self::createMock(Client::class);
        $client->method('send')->willReturn(new Response(200, [], ''));

        $factory = self::createMock(CurlClientFactory::class);
        $factory->method('create')->willReturn($client);

        $manager = new CurlManager($factory);
        $manager->setMetricsSender($loader);
        $dto = new RequestDto(new Uri('http://google.com'), 'GET', new ProcessDto());
        $manager->send($dto);
    }

    /**
     * @throws CurlException
     */
    public function testSendMetricsErr(): void
    {
        $sh = self::createMock(MetricsSenderLoader::class);
        $sh->expects(self::any())->method('getSender')->willThrowException(new Exception());

        $factory = self::createMock(CurlClientFactory::class);

        $manager = new CurlManager($factory);
        $manager->setMetricsSender($sh);
        $dto = new RequestDto(new Uri('http://google.com'), 'GET', new ProcessDto());

        self::expectException(Exception::class);
        $manager->send($dto);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Utils\CurlMetricUtils::sendCurlMetrics
     *
     * @throws Exception
     */
    public function testSendCurlMetrics(): void
    {
        $curlSender = $this->createPartialMock(CurlSender::class, ['send']);
        $curlSender->expects(self::any())->method('send')->willReturn(TRUE);

        CurlMetricUtils::sendCurlMetrics($curlSender, ['request_duration' => 2], '1', '2', 'user', 'app');

        self::assertFake();
    }

}
