<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

use Hanaboso\Utils\Enum\EnumAbstract;

/**
 * Class StorageTypeEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
final class StorageTypeEnum extends EnumAbstract
{

    public const PERSISTENT = 'persistent';
    public const TEMPORARY  = 'temporary';
    public const PUBLIC     = 'public';

    /**
     * @var string[]
     */
    protected static array $choices = [
        self::PERSISTENT => 'Persistent',
        self::TEMPORARY  => 'Temporary',
        self::PUBLIC     => 'Public',
    ];

}
