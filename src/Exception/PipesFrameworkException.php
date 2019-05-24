<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Exception;

/**
 * Class PipesFrameworkException
 *
 * @package Hanaboso\CommonsBundle\Exception
 */
class PipesFrameworkException extends PipesFrameworkExceptionAbstract
{

    public const UNKNOWN_ERROR                = 1;
    public const REQUIRED_PARAMETER_NOT_FOUND = 2;

}
