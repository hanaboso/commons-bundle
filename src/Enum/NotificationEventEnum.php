<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

use Hanaboso\Utils\Enum\EnumAbstract;

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
    public const SUCCESS             = 'success';
    public const UNKNOWN_ERROR       = 'unknown_error';

    /**
     * @var string[]
     */
    protected static array $choices = [
        self::ACCESS_EXPIRATION   => 'Access Expiration',
        self::DATA_ERROR          => 'Data Error',
        self::SERVICE_UNAVAILABLE => 'Service Unavailable',
        self::SUCCESS             => 'Success',
        self::UNKNOWN_ERROR       => 'Unknown error',
    ];

}
