<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Metrics;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderInterface;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class MetricsLoaderTest
 *
 * @package CommonsBundleTests\Unit\Metrics
 */
final class MetricsLoaderTest extends KernelTestCaseAbstract
{

    use CustomAssertTrait;

    /**
     * @covers       \Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader::getSender
     *
     * @dataProvider metricsDataProvider
     *
     * @param string                      $env
     * @param MetricsSenderInterface|null $influxSender
     * @param MetricsSenderInterface|null $mongoSender
     * @param string|null                 $exp
     *
     * @throws Exception
     */
    public function testLoaderMissingSender(
        string $env,
        ?MetricsSenderInterface $influxSender,
        ?MetricsSenderInterface $mongoSender,
        ?string $exp,
    ): void
    {
        $loader = new MetricsSenderLoader($env, $influxSender, $mongoSender);
        if ($exp) {
            self::expectException(LogicException::class);
            self::expectExceptionMessage($exp);
        }

        $loader->getSender();
        self::assertFake();
    }

    /**
     * @return mixed[]
     *
     * @throws Exception
     */
    public function metricsDataProvider(): array
    {
        /** @var MetricsSenderInterface|MockObject $sender */
        $sender = self::createMock(MetricsSenderInterface::class);

        return [
            ['influx', $sender, NULL, NULL],
            ['mongo', NULL, $sender, NULL],
            ['influx', NULL, $sender, 'Influx metrics sender has not been set.'],
            ['mongo', $sender, NULL, 'Mongo metrics sender has not been set.'],
            [
                'asd', $sender, NULL,
                'Environment [METRICS_SERVICE=asd] is not a valid option. Valid options are: [influx, mongo]',
            ],
        ];
    }

}
