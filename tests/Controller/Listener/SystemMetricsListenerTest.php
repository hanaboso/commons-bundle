<?php declare(strict_types=1);

namespace CommonsBundleTests\Controller\Listener;

use CommonsBundleTests\ControllerTestCaseAbstract;
use Hanaboso\CommonsBundle\Listener\SystemMetricsListener;
use Hanaboso\CommonsBundle\Utils\CurlMetricUtils;
use Hanaboso\Utils\System\PipesHeaders;
use Hanaboso\Utils\System\SystemUsage;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SystemMetricsListenerTest
 *
 * @package CommonsBundleTests\Controller\Listener
 */
final class SystemMetricsListenerTest extends ControllerTestCaseAbstract
{

    /**
     *
     */
    public function testListenerWithoutPipesHeader(): void
    {
        $this->sendRequest('GET', '/test/route');

        /** @var Request $request */
        $request = $this->client->getRequest();

        self::assertArrayNotHasKey(SystemMetricsListener::METRICS_ATTRIBUTES_KEY, $request->attributes->all());
    }

    /**
     *
     */
    public function testListenerWithPipesHeader(): void
    {
        $headers = [
            sprintf('HTTP_%s', PipesHeaders::createKey(PipesHeaders::TOPOLOGY_ID))    => 'topoId',
            sprintf('HTTP_%s', PipesHeaders::createKey(PipesHeaders::CORRELATION_ID)) => 'correlationId',
            sprintf('HTTP_%s', PipesHeaders::createKey(PipesHeaders::NODE_ID))        => 'nodeId',
        ];
        $this->sendRequest('GET', '/test/route', [], $headers);

        /** @var Request $request */
        $request = $this->client->getRequest();

        self::assertArrayHasKey(SystemMetricsListener::METRICS_ATTRIBUTES_KEY, $request->attributes->all());

        $metrics = $request->attributes->get(SystemMetricsListener::METRICS_ATTRIBUTES_KEY);

        self::assertArrayHasKey(CurlMetricUtils::KEY_TIMESTAMP, $metrics);
        $timestamp = $metrics[CurlMetricUtils::KEY_TIMESTAMP];
        self::assertGreaterThan(SystemUsage::getCurrentTimestamp() - 5_000, $timestamp);
        self::assertArrayHasKey(CurlMetricUtils::KEY_CPU, $metrics);
        $cpu = $metrics[CurlMetricUtils::KEY_CPU];
        self::assertGreaterThanOrEqual(0, $cpu[SystemUsage::CPU_TIME_USER]);
        self::assertGreaterThanOrEqual(0, $cpu[SystemUsage::CPU_TIME_KERNEL]);
        self::assertGreaterThanOrEqual(0, $cpu[SystemUsage::CPU_START_TIME]);
    }

}
