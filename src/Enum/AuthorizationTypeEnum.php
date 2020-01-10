<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

use Hanaboso\Utils\Enum\EnumAbstract;

/**
 * Class AuthorizationTypeEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
final class AuthorizationTypeEnum extends EnumAbstract
{

    public const BASIC  = 'basic';
    public const OAUTH  = 'oauth';
    public const OAUTH2 = 'oauth2';

    /**
     * @var string[]
     */
    protected static array $choices = [
        self::BASIC  => self::BASIC,
        self::OAUTH  => self::OAUTH,
        self::OAUTH2 => self::OAUTH2,
    ];

}
