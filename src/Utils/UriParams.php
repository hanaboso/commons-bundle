<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Utils;

/**
 * Class UriParams
 *
 * @package Hanaboso\CommonsBundle\Utils
 */
final class UriParams
{

    /**
     * @param string|null $orderBy
     *
     * @return mixed[]
     */
    public static function parseOrderBy(?string $orderBy = NULL): array
    {
        $convertTable = [
            '-' => 'DESC',
            '+' => 'ASC',
        ];

        $sort = [];

        if (!empty($orderBy)) {
            foreach (explode(',', $orderBy) as $item) {
                $name        = substr($item, 0, -1);
                $direction   = substr($item, -1);
                $sort[$name] = $convertTable[$direction];
            }
        }

        return $sort;
    }

}
