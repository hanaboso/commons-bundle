<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Curl;

use Hanaboso\Utils\Exception\PipesFrameworkExceptionAbstract;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class CurlException
 *
 * @package Hanaboso\CommonsBundle\Transport\Curl
 */
final class CurlException extends PipesFrameworkExceptionAbstract
{

    public const INVALID_METHOD = self::OFFSET + 1;
    public const BODY_ON_GET    = self::OFFSET + 2;
    public const REQUEST_FAILED = self::OFFSET + 3;

    protected const OFFSET = 300;

    /**
     * CurlException constructor.
     *
     * @param string                 $message
     * @param int                    $code
     * @param Throwable|null         $previous
     * @param ResponseInterface|null $response
     */
    public function __construct(
        $message = '',
        $code = 0,
        ?Throwable $previous = NULL,
        private ?ResponseInterface $response = NULL,
    )
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

}
