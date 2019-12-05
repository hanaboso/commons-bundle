<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Utils;

use Throwable;

/**
 * Class ExceptionContextLoader
 *
 * @package Hanaboso\CommonsBundle\Utils
 */
class ExceptionContextLoader
{

    /**
     * @param Throwable $e
     *
     * @return mixed[]
     */
    public static function getContextForLogger(?Throwable $e = NULL): array
    {
        if ($e === NULL) {
            return [];
        }

        return [
            'exception' => $e,
            'message'   => $e->getMessage(),
            'trace'     => $e->getTraceAsString(),
            'code'      => $e->getCode(),
        ];
    }

}
