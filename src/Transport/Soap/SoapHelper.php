<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Soap;

use GuzzleHttp\Utils;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\RequestDtoAbstract;
use InvalidArgumentException;
use SoapHeader;
use SoapParam;
use SoapVar;
use Symfony\Component\HttpFoundation\HeaderBag;

/**
 * Class SoapHelper
 *
 * @package Hanaboso\CommonsBundle\Transport\Soap
 */
final class SoapHelper
{

    /**
     * @param RequestDtoAbstract $request
     *
     * @return SoapHeader[]
     */
    public static function composeRequestHeaders(RequestDtoAbstract $request): array
    {
        $requestHeader = $request->getHeader();

        if (empty($requestHeader->getParams())) {
            return [];
        }

        $headers = [];
        foreach ($requestHeader->getParams() as $key => $value) {
            $headers[] = new SoapHeader($requestHeader->getNamespace(), (string) $key, $value);
        }

        return $headers;
    }

    /**
     * @param RequestDtoAbstract $request
     *
     * @return mixed[]|null
     */
    public static function composeArguments(RequestDtoAbstract $request): ?array
    {
        if ($request->getType() == SoapManagerInterface::MODE_WSDL) {
            return $request->getArguments();
        } else {
            return self::composeArgumentsForNonWsdl($request);
        }
    }

    /**
     * @param string|null $headers
     *
     * @return mixed[]
     */
    public static function parseResponseHeaders(?string $headers = NULL): array
    {
        $result = [
            'headers'    => NULL,
            'reason'     => NULL,
            'statusCode' => NULL,
            'version'    => NULL,
        ];

        if ($headers === NULL) {
            return $result;
        }

        $headers = explode("\n", $headers);
        $parts   = explode(' ', array_shift($headers), 3);

        if (count($parts) > 2) {
            $result['version']    = explode('/', $parts[0])[1];
            $result['statusCode'] = $parts[1];
            $result['reason']     = $parts[2] ?? NULL;
        }

        $result['headers'] = new HeaderBag(Utils::headersFromLines($headers));

        return $result;
    }

    /**
     * @param RequestDtoAbstract $request
     *
     * @return mixed[]|null
     */
    private static function composeArgumentsForNonWsdl(RequestDtoAbstract $request): ?array
    {
        if (empty($request->getArguments())) {
            return NULL;
        }

        $soapParams = [];
        foreach ($request->getArguments() as $key => $value) {
            $soapParams[] = new SoapParam(self::composeDataForSoapParam((string) $key, $value), (string) $key);
        }

        return $soapParams;
    }

    /**
     * TODO may need to edit when implementing
     *
     * @param string $nodeName
     * @param mixed  $data
     *
     * @return SoapVar
     * @throws InvalidArgumentException
     */
    private static function composeDataForSoapParam(string $nodeName, mixed $data): SoapVar
    {
        if (!str_contains($nodeName, ':')) {
            $nodeName = sprintf('ns1:%s', $nodeName);
        }

        if (is_scalar($data)) {
            return new SoapVar($data, XSD_STRING, '', '', $nodeName);
        } else if (is_array($data)) {
            $params = [];
            foreach ($data as $subName => $subArg) {
                $params[] = self::composeDataForSoapParam($subName, $subArg);
            }

            return new SoapVar($params, SOAP_ENC_OBJECT, $nodeName, '', $nodeName);
        }

        throw new InvalidArgumentException(sprintf('Type %s is not supported.', gettype($data)));
    }

}
