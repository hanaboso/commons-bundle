<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

/**
 * Class AuthorizationTypeEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
enum AuthorizationTypeEnum: string
{

    case BASIC  = 'basic';
    case OAUTH  = 'oauth';
    case OAUTH2 = 'oauth2';

}
