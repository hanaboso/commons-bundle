<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Monolog;

/**
 * Class LogstashFormatterFactory
 *
 * @package Hanaboso\CommonsBundle\Monolog
 */
class LogstashFormatterFactory
{

    /**
     * @param string $serviceType
     *
     * @return LogstashFormatter
     */
    public function create(string $serviceType): LogstashFormatter
    {
        return new LogstashFormatter($serviceType);
    }

}
