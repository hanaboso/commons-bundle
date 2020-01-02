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
                'user'     => $parsedUrl[1],
                'password' => $parsedUrl[2],
                'host'     => $parsedUrl[3],
            ];

            if (!empty($queryArr)) {
                $result = array_merge($result, $queryArr);
            }

            if ((isset($parsedUrl[7]) && !empty($parsedUrl[7])) || (isset($parsedUrl[5]) && !empty($parsedUrl[5]))) {
                $result['port'] = isset($parsedUrl[7]) && !empty($parsedUrl[7]) ? $parsedUrl[7] : $parsedUrl[5];
            }

            if ((isset($parsedUrl[6]) && !empty($parsedUrl[6])) || (isset($parsedUrl[4]) && !empty($parsedUrl[4]))) {
                $result['vhost'] = isset($parsedUrl[6]) && !empty($parsedUrl[6]) ? $parsedUrl[6] : $parsedUrl[4];
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
                'host' => $parsedUrl[1],
            ];

            if (!empty($queryArr)) {
                $result = array_merge($result, $queryArr);
            }

            if ((isset($parsedUrl[5]) && !empty($parsedUrl[5])) || (isset($parsedUrl[3]) && !empty($parsedUrl[3]))) {
                $result['port'] = isset($parsedUrl[5]) && !empty($parsedUrl[5]) ? $parsedUrl[5] : $parsedUrl[2];
            }

            if ((isset($parsedUrl[2]) && !empty($parsedUrl[2])) || (isset($parsedUrl[4]) && !empty($parsedUrl[4]))) {
                $result['vhost'] = isset($parsedUrl[2]) && !empty($parsedUrl[2]) ? $parsedUrl[2] : $parsedUrl[4];
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
                $queryArr[$query[0]] = (int) $query[1];
            }
        }

        return $queryArr;
    }

}