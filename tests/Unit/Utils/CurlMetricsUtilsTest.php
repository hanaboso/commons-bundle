<?php declare(strict_types=1);

namespace Tests\Unit\Utils;

use Exception;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Enum\MetricsEnum;
use Hanaboso\CommonsBundle\Metrics\InfluxDbSender;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;
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
        $influx = $this->createMock(InfluxDbSender::class);
        $influx
            ->expects(self::any())
            ->method('send')->will($this->returnCallback(
                function (array $times, array $data): bool {
                    $data;

                    self::assertGreaterThan(0, $times[MetricsEnum::REQUEST_TOTAL_DURATION_SENT]);

                    return TRUE;
                }
            ));
        self::$container->set('hbpf.influxdb_sender_connector', $influx);

        /** @var CurlManager $manager */
        $manager = self::$container->get('hbpf.transport.curl_manager');
        $dto     = new RequestDto('GET', new Uri('http://google.com'));
        $manager->send($dto);
    }

}
