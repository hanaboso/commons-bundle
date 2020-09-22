<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Crypt\Exceptions;

use Hanaboso\Utils\Exception\PipesFrameworkExceptionAbstract;

/**
 * Class CryptException
 *
 * @package Hanaboso\CommonsBundle\Crypt\Exceptions
 */
final class CryptException extends PipesFrameworkExceptionAbstract
{

    public const UNKNOWN_PREFIX    = self::OFFSET + 1;
    public const REMOVED_PREFIX    = self::OFFSET + 2;
    public const BAD_PREFIX_LENGTH = self::OFFSET + 3;

    protected const OFFSET = 1_300;

}
