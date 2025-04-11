<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Soap;

/**
 * Interface SoapManagerInterface
 *
 * @package Hanaboso\CommonsBundle\Transport\Soap
 */
interface SoapManagerInterface
{

    public const string MODE_WSDL     = 'wsdl';
    public const string MODE_NON_WSDL = 'non-wsdl';

}
