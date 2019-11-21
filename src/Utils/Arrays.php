<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Utils;

/**
 * Class Arrays
 *
 * @package Hanaboso\CommonsBundle\Utils
 */
final class Arrays
{

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public static function isList($value): bool
    {
        return is_array($value) && (!$value || array_keys($value) === range(0, count($value) - 1));
    }

}