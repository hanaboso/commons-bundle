<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

/**
 * Class NotificationEventEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
enum NotificationEventEnum: string
{

    case ACCESS_EXPIRATION   = 'access_expiration';
    case DATA_ERROR          = 'data_error';
    case SERVICE_UNAVAILABLE = 'service_unavailable';
    case SUCCESS             = 'success';
    case UNKNOWN_ERROR       = 'unknown_error';

}
