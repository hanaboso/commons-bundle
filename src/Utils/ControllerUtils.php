<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Utils;

use Hanaboso\CommonsBundle\Exception\PipesFrameworkException;
use Throwable;

/**
 * Class ControllerUtils
 *
 * @package Hanaboso\CommonsBundle\Utils
 */
class ControllerUtils
{

    public const INTERNAL_SERVER_ERROR = 'INTERNAL_SERVER_ERROR';
    public const BAD_CREDENTIALS       = 'BAD_CREDENTIALS';
    public const UNAUTHORIZED          = 'UNAUTHORIZED';
    public const NOT_LOGGED            = 'NOT_LOGGED';
    public const INVALID_REQUEST       = 'INVALID_REQUEST';
    public const SERVICE_UNAVAILABLE   = 'SERVICE_UNAVAILABLE';
    public const EMPTY                 = 'EMPTY';
    public const NOT_FOUND             = 'NOT_FOUND';
    public const INVALID_OPERATION     = 'INVALID_OPERATION';
    public const ENTITY_ALREADY_EXISTS = 'ENTITY_ALREADY_EXISTS';
    public const NOT_ALLOWED           = 'NOT_ALLOWED';

    /**
     * @param Throwable $e
     * @param string    $status
     *
     * @return string
     */
    public static function createExceptionData(Throwable $e, string $status = self::INTERNAL_SERVER_ERROR): string
    {
        $output = [
            'status'     => $status,
            'error_code' => $e->getCode(),
            'type'       => get_class($e),
            'message'    => $e->getMessage(),
        ];

        return Json::encode($output);
    }

    /**
     * @param mixed[]        $headers
     * @param Throwable|null $e
     *
     * @return mixed[]
     */
    public static function createHeaders(array $headers = [], ?Throwable $e = NULL): array
    {
        $code    = 0;
        $message = '';
        $detail  = '';

        if ($e) {
            $code    = $e->getCode();
            $message = $e->getMessage();
            $detail  = Json::encode($e->getTraceAsString());
        }

        $array = [
            PipesHeaders::createKey(PipesHeaders::RESULT_CODE)    => $code,
            PipesHeaders::createKey(PipesHeaders::RESULT_MESSAGE) => $message,
            PipesHeaders::createKey(PipesHeaders::RESULT_DETAIL)  => $detail,
        ];

        return array_merge($array, PipesHeaders::clear($headers));
    }

    /**
     * @param mixed[] $parameters
     * @param mixed[] $data
     *
     * @throws PipesFrameworkException
     */
    public static function checkParameters(array $parameters, array $data): void
    {
        foreach ($parameters as $parameter) {
            if (!isset($data[$parameter])) {
                throw new PipesFrameworkException(
                    sprintf('Required parameter \'%s\' not found', $parameter),
                    PipesFrameworkException::REQUIRED_PARAMETER_NOT_FOUND
                );
            }
        }
    }

}
