<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: venca
 * Date: 13.9.17
 * Time: 22:59
 */

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