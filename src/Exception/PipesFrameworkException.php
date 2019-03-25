<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.3.2017
 * Time: 11:49
 */

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
