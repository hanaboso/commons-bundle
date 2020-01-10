<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Crypt\Exceptions;

use Hanaboso\Utils\Exception\PipesFrameworkExceptionAbstract;

/**
 * Class CryptException
 *
 * @package Hanaboso\CommonsBundle\Crypt\Exceptions
 */
class CryptException extends PipesFrameworkExceptionAbstract
{

    protected const OFFSET = 1_300;

    public const UNKNOWN_PREFIX = self::OFFSET + 1;
    public const REMOVED_PREFIX = self::OFFSET + 2;

}
