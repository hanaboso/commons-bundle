<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Soap\Dto\Wsdl;

use Hanaboso\CommonsBundle\Transport\Soap\Dto\RequestDtoAbstract;
use Hanaboso\CommonsBundle\Transport\Soap\SoapManagerInterface;

/**
 * Class RequestDto
 *
 * @package Hanaboso\CommonsBundle\Transport\Soap\Dto\Wsdl
 */
class RequestDto extends RequestDtoAbstract
{

    /**
     * @return string
     */
    public function getType(): string
    {
        return SoapManagerInterface::MODE_WSDL;
    }

}