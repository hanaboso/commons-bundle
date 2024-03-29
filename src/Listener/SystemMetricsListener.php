<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Listener;

use Exception;
use Hanaboso\CommonsBundle\Enum\MetricsEnum;
use Hanaboso\CommonsBundle\Metrics\Exception\SystemMetricException;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
use Hanaboso\CommonsBundle\Utils\CurlMetricUtils;
use Hanaboso\Utils\String\LoggerFormater;
use Hanaboso\Utils\System\PipesHeaders;
use Hanaboso\Utils\Traits\LoggerTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class SystemMetricsListener
 *
 * @package Hanaboso\CommonsBundle\Listener
 */
final class SystemMetricsListener implements EventSubscriberInterface, LoggerAwareInterface
{

    use LoggerTrait;

    public const METRICS_ATTRIBUTES_KEY = 'system_metrics';

    /**
     * SystemMetricsListener constructor.
     *
     * @param MetricsSenderLoader $metricsSender
     */
    public function __construct(private MetricsSenderLoader $metricsSender)
    {
        $this->logger = new NullLogger();
    }

    /**
     * Adds system metrics values to request object
     *
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event): void
    {
        try {
            if (!$event->isMainRequest() || !$this->isPipesRequest($event->getRequest())) {
                return;
            }

            $event->getRequest()->attributes->add(
                [self::METRICS_ATTRIBUTES_KEY => CurlMetricUtils::getCurrentMetrics()],
            );
        } catch (Exception $e) {
            $this->logger->error(
                'Metrics listener onKernelController exception',
                LoggerFormater::getContextForLogger($e),
            );
        }
    }

    /**
     * @param TerminateEvent $event
     */
    public function onKernelTerminate(TerminateEvent $event): void
    {
        try {
            if (!$event->isMainRequest() || !$this->isPipesRequest($event->getRequest())) {
                return;
            }
            if (!$event->getRequest()->attributes->has(self::METRICS_ATTRIBUTES_KEY)) {
                throw new SystemMetricException('Initial system metrics not found.');
            }

            $this->sendMetrics($event->getRequest());
        } catch (Exception $e) {
            $this->logger->error(
                'Metrics listener onKernelTerminate exception',
                LoggerFormater::getContextForLogger($e),
            );
        }
    }

    /**
     * @return array<string, array<int|string, array<int|string, int|string>|int|string>|string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::TERMINATE  => 'onKernelTerminate',
        ];
    }

    /**
     * ------------------------------------------ HELPERS ---------------------------------------
     */

    /**
     * @param Request $request
     *
     * @throws Exception
     */
    private function sendMetrics(Request $request): void
    {
        $headers = $request->headers;
        $times   = CurlMetricUtils::getTimes($request->attributes->get(self::METRICS_ATTRIBUTES_KEY));

        $this->metricsSender->getSender()->send(
            [
                MetricsEnum::CPU_KERNEL_TIME->value        => $times[CurlMetricUtils::KEY_KERNEL_TIME],
                MetricsEnum::CPU_USER_TIME->value          => $times[CurlMetricUtils::KEY_USER_TIME],
                MetricsEnum::REQUEST_TOTAL_DURATION->value => $times[CurlMetricUtils::KEY_REQUEST_DURATION],
            ],
            [
                MetricsEnum::CORRELATION_ID->value => $headers->get(PipesHeaders::CORRELATION_ID),
                MetricsEnum::NODE_ID->value        => $headers->get(PipesHeaders::NODE_ID),
                MetricsEnum::TOPOLOGY_ID->value    => $headers->get(PipesHeaders::TOPOLOGY_ID),
            ],
        );
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function isPipesRequest(Request $request): bool
    {
        return $request->headers->has(PipesHeaders::TOPOLOGY_ID)
            && $request->headers->has(PipesHeaders::CORRELATION_ID)
            && $request->headers->has(PipesHeaders::NODE_ID);
    }

}
