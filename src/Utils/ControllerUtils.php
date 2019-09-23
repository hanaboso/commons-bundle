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
     * @param Throwable $exception
     * @param string    $status
     *
     * @return string|array
     */
    public static function createExceptionData(Throwable $exception, string $status = self::INTERNAL_SERVER_ERROR)
    {
        $output = [
            'status'     => $status,
            'error_code' => 2001,
            'type'       => get_class($exception),
            'message'    => $exception->getMessage(),
        ];

        return (string) json_encode($output);
    }

    /**
     * @param array          $headers
     * @param Throwable|null $e
     *
     * @return array
     */
    public static function createHeaders(array $headers = [], ?Throwable $e = NULL): array
    {
        $code    = 0;
        $message = '';
        $detail  = '';

        if ($e) {
            $code    = 2001;
            $message = $e->getMessage();
            $detail  = json_encode($e->getTraceAsString());
        }

        $array = [
            PipesHeaders::createKey(PipesHeaders::RESULT_CODE)    => $code,
            PipesHeaders::createKey(PipesHeaders::RESULT_MESSAGE) => $message,
            PipesHeaders::createKey(PipesHeaders::RESULT_DETAIL)  => $detail,
        ];

        return array_merge($array, PipesHeaders::clear($headers));
    }

    /**
     * @param array $parameters
     * @param array $data
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