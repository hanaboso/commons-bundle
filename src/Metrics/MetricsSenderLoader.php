<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Metrics;

use LogicException;

/**
 * Class MetricsSenderLoader
 *
 * @package Hanaboso\CommonsBundle\Metrics
 */
final class MetricsSenderLoader
{

    private const INFLUX = 'influx';
    private const MONGO  = 'mongo';

    /**
     * MetricsSenderLoader constructor.
     *
     * @param string                      $metricsService
     * @param MetricsSenderInterface|null $influxSender
     * @param MetricsSenderInterface|null $mongoSender
     */
    public function __construct(
        private string $metricsService,
        private ?MetricsSenderInterface $influxSender,
        private ?MetricsSenderInterface $mongoSender,
    )
    {
    }

    /**
     * @return MetricsSenderInterface
     */
    public function getSender(): MetricsSenderInterface
    {
        switch ($this->metricsService) {
            case self::INFLUX:
                if (!$this->influxSender) {
                    throw new LogicException('Influx metrics sender has not been set.');
                }

                return $this->influxSender;
            case self::MONGO:
                if (!$this->mongoSender) {
                    throw new LogicException('Mongo metrics sender has not been set.');
                }

                return $this->mongoSender;
            default:
                throw new LogicException(
                    sprintf(
                        'Environment [METRICS_SERVICE=%s] is not a valid option. Valid options are: [%s]',
                        $this->metricsService,
                        implode(', ', [self::INFLUX, self::MONGO]),
                    ),
                );
        }
    }

}
