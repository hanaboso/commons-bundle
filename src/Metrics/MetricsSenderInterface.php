<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Metrics;

/**
 * Interface MetricsSenderInterface
 *
 * @package Hanaboso\CommonsBundle\Metrics
 */
interface MetricsSenderInterface
{

    /**
     * @param mixed[] $fields
     * @param mixed[] $tags
     * @param bool    $isProcessMetrics
     *
     * @return bool
     */
    public function send(array $fields, array $tags, bool $isProcessMetrics = TRUE): bool;

}
