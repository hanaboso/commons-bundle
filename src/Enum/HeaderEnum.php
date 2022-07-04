<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

use Hanaboso\Utils\Enum\EnumAbstract;

/**
 * Class HeaderEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
final class HeaderEnum extends EnumAbstract
{

    public const USER = 'user';

    /**
     * @var string[]
     */
    protected static array $choices = [
        self::USER => 'user',
    ];

}
