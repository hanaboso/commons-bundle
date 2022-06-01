<?php declare(strict_types=1);

namespace CommonsBundleTests\Controller\Listener;

use CommonsBundleTests\ControllerTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Listener\SystemMetricsListener;
use Hanaboso\CommonsBundle\Utils\CurlMetricUtils;
use Hanaboso\Utils\System\PipesHeaders;
use Hanaboso\Utils\System\SystemUsage;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class SystemMetricsListenerTest
 *
 * @package CommonsBundleTests\Controller\Listener
 */
final class SystemMetricsListenerTest extends ControllerTestCaseAbstract
{

    /**
     * @throws Exception
     */
    public function testListenerWithoutPipesHeader(): void
    {
        $this->sendRequest('GET', '/test/route');

        $request = $this->client->getRequest();

        self::assertArrayNotHasKey(SystemMetricsListener::METRICS_ATTRIBUTES_KEY, $request->attributes->all());
    }

    /**
     * @throws Exception
     */
    public function testListenerWithPipesHeader(): void
    {
        $headers = [
            sprintf('HTTP_%s', PipesHeaders::createKey(PipesHeaders::TOPOLOGY_ID))    => 'topoId',
            sprintf('HTTP_%s', PipesHeaders::createKey(PipesHeaders::CORRELATION_ID)) => 'correlationId',
            sprintf('HTTP_%s', PipesHeaders::createKey(PipesHeaders::NODE_ID))        => 'nodeId',
        ];
        $this->sendRequest('GET', '/test/route', [], $headers);

        $request = $this->client->getRequest();

        self::assertArrayHasKey(SystemMetricsListener::METRICS_ATTRIBUTES_KEY, $request->attributes->all());

        $metrics = $request->attributes->get(SystemMetricsListener::METRICS_ATTRIBUTES_KEY);

        self::assertArrayHasKey(CurlMetricUtils::KEY_TIMESTAMP, $metrics);
        $timestamp = $metrics[CurlMetricUtils::KEY_TIMESTAMP];
        self::assertGreaterThan(SystemUsage::getCurrentTimestamp() - 10_000, $timestamp);
        self::assertArrayHasKey(CurlMetricUtils::KEY_CPU, $metrics);
        $cpu = $metrics[CurlMetricUtils::KEY_CPU];
        self::assertGreaterThanOrEqual(0, $cpu[SystemUsage::CPU_TIME_USER]);
        self::assertGreaterThanOrEqual(0, $cpu[SystemUsage::CPU_TIME_KERNEL]);
        self::assertGreaterThanOrEqual(0, $cpu[SystemUsage::CPU_START_TIME]);
    }

    /**
     *
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            [
                KernelEvents::TERMINATE  => 'onKernelTerminate',
                KernelEvents::CONTROLLER => 'onKernelController',
            ],
            SystemMetricsListener::getSubscribedEvents(),
        );
    }

}
