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
     * @var MetricsSenderInterface|null
     */
    private $influxSender;

    /**
     * @var MetricsSenderInterface|null
     */
    private $mongoSender;

    /**
     * @var string
     */
    private $metricsService;

    /**
     * MetricsSenderLoader constructor.
     *
     * @param string                      $metricsService
     * @param MetricsSenderInterface|null $influxSender
     * @param MetricsSenderInterface|null $mongoSender
     */
    public function __construct(
        string $metricsService,
        ?MetricsSenderInterface $influxSender,
        ?MetricsSenderInterface $mongoSender
    )
    {

        $this->metricsService = $metricsService;
        $this->influxSender   = $influxSender;
        $this->mongoSender    = $mongoSender;
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
                        implode(', ', [self::INFLUX, self::MONGO])
                    )
                );
        }
    }

}