<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Utils;

use Throwable;

/**
 * Class ExceptionContextLoader
 */
class ExceptionContextLoader
{

    /**
     * @param Throwable $e
     *
     * @return array
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