<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

use Hanaboso\Utils\Enum\EnumAbstract;

/**
 * Class NotificationSenderEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
final class NotificationSenderEnum extends EnumAbstract
{

    public const CURL   = 'curl';
    public const EMAIL  = 'email';
    public const RABBIT = 'rabbit';

    /**
     * @var string[]
     */
    protected static array $choices = [
        self::CURL   => self::CURL,
        self::EMAIL  => self::EMAIL,
        self::RABBIT => self::RABBIT,
    ];

}
