<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Curl\Dto;

use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\Utils\System\PipesHeaders;

/**
 * Class RequestDto
 *
 * @package Hanaboso\CommonsBundle\Transport\Curl\Dto
 */
final class RequestDto
{

    /**
     * @var string
     */
    private string $body;

    /**
     * @var mixed[]
     */
    private array $debugInfo;

    /**
     * RequestDto constructor.
     *
     * @param string  $method
     * @param Uri     $uri
     * @param mixed[] $headers
     *
     * @throws CurlException
     */
    public function __construct(private string $method, private Uri $uri, private array $headers = [])
    {
        if (!in_array($method, CurlManager::getMethods(), TRUE)) {
            throw new CurlException(
                sprintf('Method %s is not a valid curl method', $method),
                CurlException::INVALID_METHOD,
            );
        }

        $this->debugInfo = PipesHeaders::debugInfo($headers);
        $this->body      = '';
    }

    /**
     * @param RequestDto  $dto
     * @param Uri|null    $uri
     * @param string|null $method
     *
     * @return RequestDto
     * @throws CurlException
     */
    public static function from(RequestDto $dto, ?Uri $uri = NULL, ?string $method = NULL): RequestDto
    {
        $self = new self($method ?? $dto->getMethod(), $uri ?? new Uri((string) $dto->getUri(TRUE)));
        $self->setHeaders($dto->getHeaders());
        $self->debugInfo = $dto->getDebugInfo();

        return $self;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param bool $asString
     *
     * @return Uri|string
     */
    public function getUri($asString = FALSE): Uri|string
    {
        if ($asString) {
            return $this->getUriString();
        }

        return $this->uri;
    }

    /**
     * @return string
     */
    public function getUriString(): string
    {
        return (string) $this->uri;
    }

    /**
     * @param Uri $uri
     *
     * @return RequestDto
     */
    public function setUri(Uri $uri): RequestDto
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     *
     * @return $this
     * @throws CurlException
     */
    public function setBody(string $body): self
    {
        if ($this->method == CurlManager::METHOD_GET) {
            throw new CurlException('Setting body on GET method.', CurlException::BODY_ON_GET);
        }

        $this->body = $body;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param mixed[] $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getDebugInfo(): array
    {
        return $this->debugInfo;
    }

    /**
     * @param ProcessDto $dto
     *
     * @return RequestDto
     */
    public function setDebugInfo(ProcessDto $dto): RequestDto
    {
        $this->debugInfo = PipesHeaders::debugInfo($dto->getHeaders());

        return $this;
    }

}
