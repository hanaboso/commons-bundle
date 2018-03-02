<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

/**
 * Class StatusEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
final class StatusEnum extends EnumAbstract
{

    public const NEW      = 'New';
    public const STARTING = 'Starting';
    public const RUNNING  = 'Running';
    public const STOPPED  = 'Stopped';

    /**
     * @var string[]
     */
    protected static $choices = [
        self::NEW      => 'New',
        self::STARTING => 'Starting',
        self::RUNNING  => 'Running',
        self::STOPPED  => 'Stopped',
    ];

}