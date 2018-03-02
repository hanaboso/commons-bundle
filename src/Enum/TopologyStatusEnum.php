<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

/**
 * Class TopologyStatusEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
final class TopologyStatusEnum extends EnumAbstract
{

    public const DRAFT  = 'draft';
    public const PUBLIC = 'public';

    /**
     * @var string[]
     */
    protected static $choices = [
        self::DRAFT  => 'draft',
        self::PUBLIC => 'public',
    ];

}