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

    /**
     * MetricsSenderLoader constructor.
     *
     * @param MetricsSenderInterface|null $mongoSender
     */
    public function __construct(private ?MetricsSenderInterface $mongoSender)
    {
    }

    /**
     * @return MetricsSenderInterface
     */
    public function getSender(): MetricsSenderInterface
    {
        if (!$this->mongoSender) {
            throw new LogicException('Mongo metrics sender has not been set.');
        }

        return $this->mongoSender;
    }

}
