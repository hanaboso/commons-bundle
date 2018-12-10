<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: radek.jirsa
 * Date: 10.12.18
 * Time: 10:56
 */

namespace Hanaboso\CommonsBundle\Utils;

use DateTime;
use DateTimeZone;
use Hanaboso\CommonsBundle\Exception\DateTimeException;
use Throwable;

/**
 * Class DateTimeUtils
 *
 * @package Hanaboso\CommonsBundle\Utils
 */
final class DateTimeUtils
{

    /**
     * @param string $dateTime
     *
     * @return DateTime
     * @throws DateTimeException
     */
    public static function getUTCDateTime(string $dateTime = 'NOW'): DateTime
    {
        try {
            return new DateTime($dateTime, new DateTimeZone('UTC'));
        } catch (Throwable $t) {
            throw new DateTimeException($t->getMessage(), $t->getCode(), $t);
        }
    }

}