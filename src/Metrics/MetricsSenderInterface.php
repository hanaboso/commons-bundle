<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Metrics;

use Exception;

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
     *
     * @return bool
     * @throws Exception
     */
    public function send(array $fields, array $tags = []): bool;

}
