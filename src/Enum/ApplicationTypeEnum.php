<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

/**
 * Class ApplicationTypeEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
enum ApplicationTypeEnum: string
{

    case CRON    = 'cron';
    case WEBHOOK = 'webhook';

    /**
     * @param string $type
     *
     * @return bool
     */
    public static function isWebhook(string $type): bool
    {
        return $type === self::WEBHOOK->value;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public static function isCron(string $type): bool
    {
        return $type === self::CRON->value;
    }

}
