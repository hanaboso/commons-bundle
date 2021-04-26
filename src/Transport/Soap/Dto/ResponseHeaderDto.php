<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Soap\Dto;

use Symfony\Component\HttpFoundation\HeaderBag;

/**
 * Class ResponseHeaderDto
 *
 * @package Hanaboso\CommonsBundle\Transport\Soap\Dto
 */
final class ResponseHeaderDto
{

    /**
     * ResponseHeaderDto constructor.
     *
     * @param HeaderBag|null $httpHeaders
     * @param string|null    $httpVersion
     * @param int|null       $httpStatusCode
     * @param string|null    $httpReason
     */
    public function __construct(
        private ?HeaderBag $httpHeaders,
        private ?string $httpVersion,
        private ?int $httpStatusCode,
        private ?string $httpReason
    )
    {
    }

    /**
     * @return HeaderBag<mixed>|null ?HeaderBag
     */
    public function getHttpHeaders(): ?HeaderBag
    {
        return $this->httpHeaders;
    }

    /**
     * @return string|null
     */
    public function getHttpVersion(): ?string
    {
        return $this->httpVersion;
    }

    /**
     * @return int|null
     */
    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode;
    }

    /**
     * @return string|null
     */
    public function getHttpReason(): ?string
    {
        return $this->httpReason;
    }

}
