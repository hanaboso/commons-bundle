<?php declare(strict_types=1);

namespace Tests\Unit\Utils;

use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Enum\MetricsEnum;
use Hanaboso\CommonsBundle\Metrics\InfluxDbSender;
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
     *
     */
    public function testCurlMetrics(): void
    {
        $influx = $this->createMock(InfluxDbSender::class);
        $influx->expects($this->once())
            ->method('send')->will($this->returnCallback(
                function (array $times, array $data): bool {
                    self::assertGreaterThan(0, $times[MetricsEnum::REQUEST_TOTAL_DURATION_SENT]);
                    self::assertNotEmpty($data[MetricsEnum::HOST]);
                    self::assertEquals(str_replace('=','',base64_encode('http://google.com')), $data[MetricsEnum::URI]);

                    return TRUE;
                }
            ));
        $this->container->set('hbpf.influxdb_sender', $influx);

        $manager = $this->container->get('hbpf.transport.curl_manager');
        $dto     = new RequestDto('GET', new Uri('http://google.com'));
        $manager->send($dto);
    }

}