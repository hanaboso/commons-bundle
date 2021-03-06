<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Exception;

use Hanaboso\Utils\Exception\PipesFrameworkExceptionAbstract;

/**
 * Class CronException
 *
 * @package Hanaboso\CommonsBundle\Exception
 */
final class CronException extends PipesFrameworkExceptionAbstract
{

    public const CRON_EXCEPTION = self::OFFSET + 1;

    protected const OFFSET = 2_700;

}
