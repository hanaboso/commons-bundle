<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

use Hanaboso\Utils\Enum\EnumAbstract;

/**
 * Class DatabaseFilterEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
final class DatabaseFilterEnum extends EnumAbstract
{

    public const DELETED = 'deleted';
    /**
     * @var string[]
     */
    protected static array $choices = [
        self::DELETED => 'deleted',
    ];

}
