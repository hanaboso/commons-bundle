<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Crypt;

use Hanaboso\CommonsBundle\Exception\PipesFrameworkException;

/**
 * Class CryptException
 *
 * @package Hanaboso\CommonsBundle\Crypt
 */
class CryptException extends PipesFrameworkException
{

    protected const OFFSET = 1300;

    public const UNKNOWN_PREFIX = self::OFFSET + 1;

}