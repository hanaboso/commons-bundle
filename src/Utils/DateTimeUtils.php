<?php declare(strict_types=1);

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
class DateTimeUtils
{

    public const DATE_TIME  = 'Y-m-d H:i:s';
    public const DATE       = 'Y-m-d';
    public const MYSQL_DATE = '%Y-%m-%d';

    /**
     * @param string $dateTime
     *
     * @return DateTime
     * @throws DateTimeException
     */
    public static function getUtcDateTime(string $dateTime = 'NOW'): DateTime
    {
        try {
            return new DateTime($dateTime, new DateTimeZone('UTC'));
        } catch (Throwable $t) {
            throw new DateTimeException($t->getMessage(), $t->getCode(), $t);
        }
    }

    /**
     * @param int $timeStamp
     *
     * @return DateTime
     */
    public static function getUtcDateTimeFromTimeStamp(int $timeStamp = 0): DateTime
    {
        /** @var DateTime $dateTime */
        $dateTime = DateTime::createFromFormat('U', (string) $timeStamp, new DateTimeZone('UTC'));

        return $dateTime;
    }

}
