<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

use Hanaboso\Utils\Enum\EnumAbstract;

/**
 * Class ApplicationTypeEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
final class ApplicationTypeEnum extends EnumAbstract
{

    public const CRON    = 'cron';
    public const WEBHOOK = 'webhook';

    /**
     * @var string[]
     */
    protected static array $choices = [
        self::CRON    => self::CRON,
        self::WEBHOOK => self::WEBHOOK,
    ];

    /**
     * @param string $type
     *
     * @return bool
     */
    public static function isWebhook(string $type): bool
    {
        return $type === self::WEBHOOK;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public static function isCron(string $type): bool
    {
        return $type === self::CRON;
    }

}
