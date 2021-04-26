<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Monolog;

use Exception;

/**
 * Class MonologFormatter
 *
 * @package Hanaboso\CommonsBundle\Monolog
 */
final class MonologFormatter
{

    /**
     * @param Exception $exception
     *
     * @return string
     */
    public static function formatException(Exception $exception): string
    {
        return sprintf('%s %s: %s', $exception::class, $exception->getCode(), $exception->getMessage());
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function formatString(string $string): string
    {
        return $string;
    }

}
