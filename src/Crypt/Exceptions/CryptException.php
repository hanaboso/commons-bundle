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

    public const int UNKNOWN_PREFIX    = self::OFFSET + 1;
    public const int REMOVED_PREFIX    = self::OFFSET + 2;
    public const int BAD_PREFIX_LENGTH = self::OFFSET + 3;

    protected const int OFFSET = 1_300;

}
