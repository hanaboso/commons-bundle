<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Soap\Dto;

/**
 * Class RequestHeaderDto
 *
 * @package Hanaboso\CommonsBundle\Transport\Soap\Dto
 */
class RequestHeaderDto
{

    /**
     * @var string
     */
    private string $namespace;

    /**
     * @var mixed[]
     */
    private array $params;

    /**
     * RequestHeaderDto constructor.
     *
     * @param string  $namespace
     * @param mixed[] $params
     */
    public function __construct(string $namespace, array $params = [])
    {
        $this->namespace = $namespace;
        $this->params    = $params;
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
    public function setParam(string $key, $value): RequestHeaderDto
    {
        $this->params[$key] = $value;

        return $this;
    }

}
