<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Soap;

use Hanaboso\Utils\Exception\PipesFrameworkExceptionAbstract;

/**
 * Class SoapException
 *
 * @package Hanaboso\CommonsBundle\Transport\Soap
 */
final class SoapException extends PipesFrameworkExceptionAbstract
{

    public const int UNKNOWN_EXCEPTION     = self::OFFSET;
    public const int UNKNOWN_SOAP_VERSION  = self::OFFSET + 1;
    public const int INVALID_FUNCTION_CALL = self::OFFSET + 2;
    public const int INVALID_WSDL          = self::OFFSET + 3;

    protected const int OFFSET = 900;

}
