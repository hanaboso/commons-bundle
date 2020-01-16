<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Soap\Dto\NonWsdl;

use Hanaboso\CommonsBundle\Transport\Soap\Dto\RequestDtoAbstract;
use Hanaboso\CommonsBundle\Transport\Soap\SoapManagerInterface;

/**
 * Class RequestDto
 *
 * @package Hanaboso\CommonsBundle\Transport\Soap\Dto\NonWsdl
 */
class RequestDto extends RequestDtoAbstract
{

    /**
     * @return string
     */
    public function getType(): string
    {
        return SoapManagerInterface::MODE_NON_WSDL;
    }

}
