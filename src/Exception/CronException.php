<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Exception;

/**
 * Class CronException
 *
 * @package Hanaboso\CommonsBundle\Exception
 */
final class CronException extends PipesFrameworkExceptionAbstract
{

    protected const OFFSET = 2_700;

    public const CRON_EXCEPTION = self::OFFSET + 1;

}