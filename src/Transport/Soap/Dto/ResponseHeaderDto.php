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
     * @var HeaderBag<mixed>|null
     */
    private ?HeaderBag $httpHeaders;

    /**
     * @var string|null
     */
    private ?string $httpVersion;

    /**
     * @var int|null
     */
    private ?int $httpStatusCode;

    /**
     * @var string|null
     */
    private ?string $httpReason;

    /**
     * ResponseHeaderDto constructor.
     *
     * @param HeaderBag<mixed> $httpHeaders
     * @param string|null      $httpVersion
     * @param int              $httpStatusCode
     * @param string|null      $httpReason
     */
    public function __construct(
        ?HeaderBag $httpHeaders,
        ?string $httpVersion,
        ?int $httpStatusCode,
        ?string $httpReason
    )
    {

        $this->httpHeaders    = $httpHeaders;
        $this->httpVersion    = $httpVersion;
        $this->httpStatusCode = $httpStatusCode;
        $this->httpReason     = $httpReason;
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
