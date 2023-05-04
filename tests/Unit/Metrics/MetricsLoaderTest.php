<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Metrics;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderInterface;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
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
     * @covers \Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader::getSender
     *
     * @throws Exception
     */
    public function testLoaderMissingSender(): void
    {
        /** @var MetricsSenderInterface|MockObject $sender */
        $sender = self::createMock(MetricsSenderInterface::class);

        $loader = new MetricsSenderLoader($sender);

        $loader->getSender();
        self::assertFake();
    }

}
