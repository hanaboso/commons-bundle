<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Exception;

/**
 * Class EnumException
 *
 * @package Hanaboso\CommonsBundle\Exception
 */
final class EnumException extends PipesFrameworkExceptionAbstract
{

    protected const OFFSET = 2000;

    public const INVALID_CHOICE = self::OFFSET + 1;

}