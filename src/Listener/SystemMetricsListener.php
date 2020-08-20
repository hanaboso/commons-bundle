<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Listener;

use Exception;
use Hanaboso\CommonsBundle\Enum\MetricsEnum;
use Hanaboso\CommonsBundle\Metrics\Exception\SystemMetricException;
use Hanaboso\CommonsBundle\Metrics\MetricsSenderLoader;
use Hanaboso\CommonsBundle\Utils\CurlMetricUtils;
use Hanaboso\Utils\String\LoggerFormater;
use Hanaboso\Utils\System\PipesHeaders;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
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

    public const METRICS_ATTRIBUTES_KEY = 'system_metrics';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MetricsSenderLoader
     */
    private MetricsSenderLoader $metricsSender;

    /**
     * SystemMetricsListener constructor.
     *
     * @param MetricsSenderLoader $metricsSender
     */
    public function __construct(MetricsSenderLoader $metricsSender)
    {
        $this->metricsSender = $metricsSender;
        $this->logger        = new NullLogger();
    }

    /**
     * Adds system metrics values to request object
     *
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event): void
    {
        try {
            if (!$event->isMasterRequest() || !$this->isPipesRequest($event->getRequest())) {
                return;
            }

            $event->getRequest()->attributes->add(
                [self::METRICS_ATTRIBUTES_KEY => CurlMetricUtils::getCurrentMetrics()]
            );
        } catch (Exception $e) {
            $this->logger->error(
                'Metrics listener onKernelController exception',
                LoggerFormater::getContextForLogger($e)
            );
        }
    }

    /**
     * @param TerminateEvent $event
     */
    public function onKernelTerminate(TerminateEvent $event): void
    {
        try {
            if (!$event->isMasterRequest() || !$this->isPipesRequest($event->getRequest())) {
                return;
            }
            if (!$event->getRequest()->attributes->has(self::METRICS_ATTRIBUTES_KEY)) {
                throw new SystemMetricException('Initial system metrics not found.');
            }

            $this->sendMetrics($event->getRequest());
        } catch (Exception $e) {
            $this->logger->error(
                'Metrics listener onKernelTerminate exception',
                LoggerFormater::getContextForLogger($e)
            );
        }
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return array<string, array<int|string, array<int|string, int|string>|int|string>|string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE  => 'onKernelTerminate',
            KernelEvents::CONTROLLER => 'onKernelController',
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
                MetricsEnum::REQUEST_TOTAL_DURATION => $times[CurlMetricUtils::KEY_REQUEST_DURATION],
                MetricsEnum::CPU_USER_TIME          => $times[CurlMetricUtils::KEY_USER_TIME],
                MetricsEnum::CPU_KERNEL_TIME        => $times[CurlMetricUtils::KEY_KERNEL_TIME],
            ],
            [
                MetricsEnum::TOPOLOGY_ID    => $headers->get(PipesHeaders::createKey(PipesHeaders::TOPOLOGY_ID)),
                MetricsEnum::CORRELATION_ID => $headers->get(PipesHeaders::createKey(PipesHeaders::CORRELATION_ID)),
                MetricsEnum::NODE_ID        => $headers->get(PipesHeaders::createKey(PipesHeaders::NODE_ID)),
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function isPipesRequest(Request $request): bool
    {
        return $request->headers->has(PipesHeaders::createKey(PipesHeaders::TOPOLOGY_ID))
            && $request->headers->has(PipesHeaders::createKey(PipesHeaders::CORRELATION_ID))
            && $request->headers->has(PipesHeaders::createKey(PipesHeaders::NODE_ID));
    }

}
