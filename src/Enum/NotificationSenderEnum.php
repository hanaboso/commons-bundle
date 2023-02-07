<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

/**
 * Class NotificationSenderEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
enum NotificationSenderEnum: string
{

    case CURL   = 'curl';
    case EMAIL  = 'email';
    case RABBIT = 'rabbit';

}
