<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Utils;

/**
 * Class TransportFormatter
 *
 * @package Hanaboso\CommonsBundle\Transport\Utils
 */
class TransportFormatter
{

    /**
     * @param mixed[] $headers
     *
     * @return string
     */
    public static function headersToString(array $headers): string
    {
        $tmpHeaders = [];
        foreach ($headers as $key => $values) {
            if (is_array($values)) {
                $tmpHeaders[] = sprintf('%s=[%s]', $key, implode(', ', $values));
            } else {
                $tmpHeaders[] = sprintf('%s=%s', $key, $values);
            }
        }

        return implode(', ', $tmpHeaders);
    }

    /**
     * @param string  $method
     * @param string  $url
     * @param mixed[] $headers
     * @param string  $body
     *
     * @return string
     */
    public static function requestToString(string $method, string $url, array $headers = [], string $body = ''): string
    {
        return sprintf(
            'Request: Method: %s, Uri: %s, Headers: %s, Body: "%s"',
            strtoupper($method),
            $url,
            self::headersToString($headers),
            $body
        );
    }

    /**
     * @param int     $statusCode
     * @param string  $reasonPhrase
     * @param mixed[] $headers
     * @param string  $body
     *
     * @return string
     */
    public static function responseToString(
        int $statusCode,
        string $reasonPhrase,
        array $headers = [],
        string $body = ''
    ): string
    {
        return sprintf(
            'Response: Status Code: %s, Reason Phrase: %s, Headers: %s, Body: "%s"',
            $statusCode,
            $reasonPhrase,
            self::headersToString($headers),
            $body
        );
    }

}
