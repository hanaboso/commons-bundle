<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Exception;

/**
 * Class CronException
 *
 * @package Hanaboso\CommonsBundle\Exception
 */
final class CronException extends PipesFrameworkException
{

    protected const OFFSET = 2700;

    public const CRON_EXCEPTION = self::OFFSET + 1;

}