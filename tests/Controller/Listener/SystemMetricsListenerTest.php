<?php declare(strict_types=1);

namespace Tests\Controller\Listener;

use Hanaboso\CommonsBundle\Listener\SystemMetricsListener;
use Hanaboso\CommonsBundle\Metrics\SystemMetrics;
use Hanaboso\CommonsBundle\Utils\CurlMetricUtils;
use Hanaboso\CommonsBundle\Utils\PipesHeaders;
use Symfony\Component\HttpFoundation\Request;
use Tests\ControllerTestCaseAbstract;

/**
 * Class SystemMetricsListenerTest
 *
 * @package Tests\Controller\Listener
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
        self::assertGreaterThan(SystemMetrics::getCurrentTimestamp() - 5000, $timestamp);
        self::assertArrayHasKey(CurlMetricUtils::KEY_CPU, $metrics);
        $cpu = $metrics[CurlMetricUtils::KEY_CPU];
        self::assertGreaterThanOrEqual(0, $cpu[SystemMetrics::CPU_TIME_USER]);
        self::assertGreaterThanOrEqual(0, $cpu[SystemMetrics::CPU_TIME_KERNEL]);
        self::assertGreaterThanOrEqual(0, $cpu[SystemMetrics::CPU_START_TIME]);
    }

}
