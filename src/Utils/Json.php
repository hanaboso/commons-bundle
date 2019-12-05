<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Utils;

/**
 * Class Json
 *
 * @package Hanaboso\CommonsBundle\Utils
 */
final class Json
{

    /**
     * @param mixed $data
     *
     * @return string
     */
    public static function encode($data): string
    {
        return (string) json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $data
     *
     * @return mixed[]
     */
    public static function decode(string $data): array
    {
        return (array) json_decode($data, TRUE, 512, JSON_THROW_ON_ERROR);
    }

}
