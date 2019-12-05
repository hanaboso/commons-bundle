<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Curl\Dto;

use Hanaboso\CommonsBundle\Utils\Json;

/**
 * Class ResponseDto
 *
 * @package Hanaboso\CommonsBundle\Transport\Curl\Dto
 */
class ResponseDto
{

    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @var string
     */
    private string $reasonPhrase;

    /**
     * @var string
     */
    private string $body;

    /**
     * @var mixed[]
     */
    private array $headers;

    /**
     * ResponseDto constructor.
     *
     * @param int     $statusCode
     * @param string  $reasonPhrase
     * @param string  $body
     * @param mixed[] $headers
     */
    public function __construct(
        int $statusCode,
        string $reasonPhrase,
        string $body,
        array $headers
    )
    {
        $this->statusCode   = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
        $this->body         = $body;
        $this->headers      = $headers;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return mixed[]
     */
    public function getJsonBody(): array
    {
        return Json::decode($this->body);
    }

    /**
     * @return mixed[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

}
