<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

/**
 * Class HandlerEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
enum HandlerEnum: string
{

    case ACTION = 'action';
    case EVENT  = 'event';

}
