<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

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
    protected static $choices = [
        self::DELETED => 'deleted',
    ];

}
