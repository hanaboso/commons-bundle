<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Exception;

/**
 * Class CategoryException
 *
 * @package Hanaboso\CommonsBundle\Exception
 */
final class CategoryException extends PipesFrameworkExceptionAbstract
{

    protected const OFFSET = 2_300;

    public const CATEGORY_NOT_FOUND = self::OFFSET + 1;
    public const CATEGORY_USED      = self::OFFSET + 2;

}
