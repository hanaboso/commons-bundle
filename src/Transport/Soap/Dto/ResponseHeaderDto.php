<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Soap\Dto;

use Symfony\Component\HttpFoundation\HeaderBag;

/**
 * Class ResponseHeaderDto
 *
 * @package Hanaboso\CommonsBundle\Transport\Soap\Dto
 */
class ResponseHeaderDto
{

    /**
     * @var HeaderBag|null
     */
    private ?HeaderBag $httpHeaders;

    /**
     * @var null|string
     */
    private ?string $httpVersion;

    /**
     * @var int|null
     */
    private ?int $httpStatusCode;

    /**
     * @var null|string
     */
    private ?string $httpReason;

    /**
     * ResponseHeaderDto constructor.
     *
     * @param HeaderBag   $httpHeaders
     * @param string|NULL $httpVersion
     * @param int         $httpStatusCode
     * @param string|NULL $httpReason
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
     * @return HeaderBag|null ?HeaderBag
     */
    public function getHttpHeaders(): ?HeaderBag
    {
        return $this->httpHeaders;
    }

    /**
     * @return null|string
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
     * @return null|string
     */
    public function getHttpReason(): ?string
    {
        return $this->httpReason;
    }

}
