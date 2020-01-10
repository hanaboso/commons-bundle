<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

use Hanaboso\Utils\Enum\EnumAbstract;

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
    protected static array $choices = [
        self::DRAFT  => 'draft',
        self::PUBLIC => 'public',
    ];

}