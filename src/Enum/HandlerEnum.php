<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

use Hanaboso\Utils\Enum\EnumAbstract;

/**
 * Class HandlerEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
final class HandlerEnum extends EnumAbstract
{

    public const ACTION = 'action';
    public const EVENT  = 'event';

    /**
     * @var string[]
     */
    protected static array $choices = [
        self::ACTION => 'action',
        self::EVENT  => 'event',
    ];

}