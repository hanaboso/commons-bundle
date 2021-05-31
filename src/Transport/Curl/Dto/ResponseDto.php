<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Curl\Dto;

use Hanaboso\Utils\String\Json;

/**
 * Class ResponseDto
 *
 * @package Hanaboso\CommonsBundle\Transport\Curl\Dto
 */
final class ResponseDto
{

    /**
     * ResponseDto constructor.
     *
     * @param int     $statusCode
     * @param string  $reasonPhrase
     * @param string  $body
     * @param mixed[] $headers
     */
    public function __construct(
        private int $statusCode,
        private string $reasonPhrase,
        private string $body,
        private array $headers,
    )
    {
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
