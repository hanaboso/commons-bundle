<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Exception;

use Hanaboso\Utils\Exception\PipesFrameworkExceptionAbstract;

/**
 * Class CategoryException
 *
 * @package Hanaboso\CommonsBundle\Exception
 */
final class CategoryException extends PipesFrameworkExceptionAbstract
{

    public const CATEGORY_NOT_FOUND = self::OFFSET + 1;
    public const CATEGORY_USED      = self::OFFSET + 2;

    protected const OFFSET = 2_300;

}
