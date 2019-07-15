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
     * @param array $fields
     * @param array $tags
     *
     * @return bool
     */
    public function send(array $fields, array $tags = []): bool;

}