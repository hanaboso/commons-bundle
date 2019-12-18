<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Utils;

use InvalidArgumentException;

/**
 * Class DsnParser
 *
 * @package Hanaboso\CommonsBundle\Utils
 */
class DsnParser
{

    /**
     * @param string $dsn
     *
     * @return mixed
     */
    public static function genericParser(string $dsn)
    {
        return parse_url($dsn);
    }

    /**
     * @param string $dsn
     *
     * @return mixed[]
     */
    public static function rabbitParser(string $dsn): array
    {
        if (strpos($dsn, 'amqp://') === FALSE) {
            throw new InvalidArgumentException(sprintf('The given AMQP DSN "%s" is invalid.', $dsn));
        }

        $queryArr = [];
        if (strpos($dsn, '@')) {
            preg_match(
                '/amqp:\/{2}([A-z, 0-9, .]+):(.*)@(?:([A-z, 0-9, .]+)|)(?:\/([A-z, 0-9, .]+)|:(?:([0-9]+)|)\/(?:([A-z, 0-9, .]+))|:(?:([0-9]+))|)(?:\?(.*)|)/',
                $dsn,
                $parsedUrl
            );

            if (isset($parsedUrl[8])) {
                $queryArr = self::getQueryParamsArr($parsedUrl[8]);
            }

            $result = [
                'username' => $parsedUrl[1],
                'password' => $parsedUrl[2],
                'host'     => $parsedUrl[3],
                'port'     => $parsedUrl[7] ?? $parsedUrl[5] ?? '',
                'vhost'    => $parsedUrl[6] ?? $parsedUrl[4] ?? '',
            ];

            if (!empty($queryArr)) {
                $result['queryParams'] = $queryArr;
            }

            return $result;
        } else {
            preg_match(
                '/amqp:\/{2}(?:([A-z, 0-9, .]+)|)(?:\/([A-z, 0-9, .]+)|:(?:([0-9]+)|)\/(?:([A-z, 0-9, .]+))|:(?:([0-9]+))|)(?:\?(.*)|)/',
                $dsn,
                $parsedUrl
            );

            if (isset($parsedUrl[6])) {
                $queryArr = self::getQueryParamsArr($parsedUrl[6]);
            }

            $result = [
                'host'  => $parsedUrl[1],
                'port'  => $parsedUrl[5] ?? $parsedUrl[3] ?? '',
                'vhost' => $parsedUrl[2] ?? $parsedUrl[4] ?? '',
            ];

            if (!empty($queryArr)) {
                $result['queryParams'] = $queryArr;
            }

            return $result;
        }
    }

    /**
     * @param string $queryString
     *
     * @return mixed[]
     */
    private static function getQueryParamsArr(string $queryString): array
    {
        $queryArr   = [];
        $queryParam = explode('&', $queryString);
        if (!empty($queryParam)) {
            foreach ($queryParam as $item) {
                $query               = explode('=', $item);
                $queryArr[$query[0]] = $query[1];
            }
        }

        return $queryArr;
    }

}