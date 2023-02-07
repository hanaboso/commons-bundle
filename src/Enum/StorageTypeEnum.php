<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

/**
 * Class StorageTypeEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
enum StorageTypeEnum: string
{

    case PERSISTENT = 'persistent';
    case TEMPORARY  = 'temporary';
    case PUBLIC     = 'public';

}
