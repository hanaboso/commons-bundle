<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

/**
 * Class StatusEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
enum StatusEnum: string
{

    case NEW      = 'New';
    case STARTING = 'Starting';
    case RUNNING  = 'Running';
    case STOPPED  = 'Stopped';

}
