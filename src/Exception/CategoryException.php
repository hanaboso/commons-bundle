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

    public const int CATEGORY_NOT_FOUND = self::OFFSET + 1;
    public const int CATEGORY_USED      = self::OFFSET + 2;

    protected const int OFFSET = 2_300;

}
