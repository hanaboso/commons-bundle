<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

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
    protected static $choices = [
        self::CURL   => self::CURL,
        self::EMAIL  => self::EMAIL,
        self::RABBIT => self::RABBIT,
    ];

}
