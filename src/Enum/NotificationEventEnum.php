<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

/**
 * Class NotificationEventEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
final class NotificationEventEnum extends EnumAbstract
{

    public const ACCESS_EXPIRATION   = 'access_expiration';
    public const DATA_ERROR          = 'data_error';
    public const SERVICE_UNAVAILABLE = 'service_unavailable';

    /**
     * @var string[]
     */
    protected static $choices = [
        self::ACCESS_EXPIRATION   => self::ACCESS_EXPIRATION,
        self::DATA_ERROR          => self::DATA_ERROR,
        self::SERVICE_UNAVAILABLE => self::SERVICE_UNAVAILABLE,
    ];

}
