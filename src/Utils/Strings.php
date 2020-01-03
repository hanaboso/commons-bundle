<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Utils;

use Transliterator;

/**
 * Class Strings
 *
 * @package Hanaboso\CommonsBundle\Utils
 */
final class Strings
{

    public const TRIM_CHARACTERS = " \t\n\r\0\x0B\u{A0}";

    /**
     * @param string $s
     * @param string $chars
     *
     * @return string
     */
    public static function trim(string $s, string $chars = self::TRIM_CHARACTERS): string
    {
        $chars = preg_quote($chars, '#');

        return preg_replace(sprintf('#^[%s]+|[%s]+$#Du', $chars, $chars), '', $s) ?: '';
    }

    /**
     * @param string $string
     * @param bool   $firstLower
     *
     * @return string
     */
    public static function toCamelCase(string $string, bool $firstLower = FALSE): string
    {
        $camelCase = preg_replace_callback(
            '#(\.\w|_\w)#',
            fn($matches) => ucfirst(mb_substr($matches[0], 1, NULL, 'UTF-8')),
            $string
        ) ?: '';

        if ($firstLower === TRUE) {
            return lcfirst($camelCase);
        }

        return ucfirst($camelCase);
    }

    /**
     * @param mixed $object
     *
     * @return string
     */
    public static function getShortClassName($object): string
    {
        return substr((string) strrchr(get_class($object), '\\'), 1);
    }

    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function endsWith(string $haystack, string $needle): bool
    {
        return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * @param string $s
     * @param bool   $lower
     *
     * @return string
     */
    public static function webalize(string $s, bool $lower = TRUE): string
    {
        $s = self::toAscii($s);
        if ($lower) {
            $s = strtolower($s);
        }
        $s = preg_replace('#[^a-z0-9]+#i', '-', $s) ?: '';

        return trim($s, '-');
    }

    /**
     * @param string $s
     *
     * @return string
     */
    public static function toAscii(string $s): string
    {
        $t = NULL;
        if ($t === NULL && class_exists('Transliterator', FALSE)) {
            $t = Transliterator::create('Any-Latin; Latin-ASCII');
        }

        $s = preg_replace('#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{2FF}\x{370}-\x{10FFFF}]#u', '', $s) ?: '';
        $s = strtr($s, '`\'"^~?', "\x01\x02\x03\x04\x05\x06");
        $s = (string) str_replace(
            ["\u{201E}", "\u{201C}", "\u{201D}", "\u{201A}", "\u{2018}", "\u{2019}", "\u{B0}"],
            ["\x03", "\x03", "\x03", "\x02", "\x02", "\x02", "\x04"],
            $s
        );
        if ($t !== NULL) {
            $s = $t->transliterate($s) ?: '';
        }
        if (ICONV_IMPL === 'glibc') {
            $s = (string) str_replace(
                ["\u{BB}", "\u{AB}", "\u{2026}", "\u{2122}", "\u{A9}", "\u{AE}"],
                ['>>', '<<', '...', 'TM', '(c)', '(R)'],
                $s
            );
            $s = iconv('UTF-8', 'WINDOWS-1250//TRANSLIT//IGNORE', $s) ?: '';
            $s = strtr(
                $s,
                "\xa5\xa3\xbc\x8c\xa7\x8a\xaa\x8d\x8f\x8e\xaf\xb9\xb3\xbe\x9c\x9a\xba\x9d\x9f\x9e\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf8\xf9\xfa\xfb\xfc\xfd\xfe\x96\xa0\x8b\x97\x9b\xa6\xad\xb7",
                'ALLSSSSTZZZallssstzzzRAAAALCCCEEEEIIDDNNOOOOxRUUUUYTsraaaalccceeeeiiddnnooooruuuuyt- <->|-.'
            );
            $s = preg_replace('#[^\x00-\x7F]++#', '', $s) ?: '';
        } else {
            $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: '';
        }
        $s = str_replace(['`', "'", '"', '^', '~', '?'], '', $s);

        return strtr($s, "\x01\x02\x03\x04\x05\x06", '`\'"^~?');
    }

}