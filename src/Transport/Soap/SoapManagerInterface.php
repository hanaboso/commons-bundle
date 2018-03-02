<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Soap;

/**
 * Interface SoapManagerInterface
 *
 * @package Hanaboso\CommonsBundle\Transport\Soap
 */
interface SoapManagerInterface
{

    public const MODE_WSDL     = 'wsdl';
    public const MODE_NON_WSDL = 'non-wsdl';

}