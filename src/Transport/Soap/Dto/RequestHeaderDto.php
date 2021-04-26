<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Soap\Dto;

/**
 * Class RequestHeaderDto
 *
 * @package Hanaboso\CommonsBundle\Transport\Soap\Dto
 */
final class RequestHeaderDto
{

    /**
     * RequestHeaderDto constructor.
     *
     * @param string  $namespace
     * @param mixed[] $params
     */
    public function __construct(private string $namespace, private array $params = [])
    {
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return mixed[]
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return RequestHeaderDto
     */
    public function setParam(string $key, mixed $value): RequestHeaderDto
    {
        $this->params[$key] = $value;

        return $this;
    }

}
