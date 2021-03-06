<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Soap\Dto;

use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Transport\Soap\SoapException;

/**
 * Class RequestDtoAbstract
 *
 * @package Hanaboso\CommonsBundle\Transport\Soap\Dto
 */
abstract class RequestDtoAbstract
{

    /**
     * @var int
     */
    protected int $version;

    /**
     * @var string|null
     */
    protected ?string $user;

    /**
     * @var string|null
     */
    protected ?string $password;

    /**
     * @var RequestHeaderDto
     */
    private RequestHeaderDto $header;

    /**
     * @return string
     */
    abstract public function getType(): string;

    /**
     * RequestDtoAbstract constructor.
     *
     * @param string  $function
     * @param mixed[] $arguments
     * @param string  $namespace
     * @param Uri     $uri
     * @param mixed[] $params
     */
    public function __construct(
        private string $function,
        private array $arguments,
        string $namespace,
        private Uri $uri,
        array $params = [],
    )
    {
        $this->user     = NULL;
        $this->password = NULL;

        $this->header = new RequestHeaderDto($namespace, $params);
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     *
     * @return $this
     * @throws SoapException
     */
    public function setVersion(int $version): self
    {
        if (!in_array($version, [SOAP_1_1, SOAP_1_2], TRUE)) {
            throw new SoapException(
                sprintf('Unknown SOAP version "%s".', $version),
                SoapException::UNKNOWN_SOAP_VERSION,
            );
        }

        $this->version = $version;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * @param string $user
     * @param string $password
     *
     * @return $this
     */
    public function setAuth(string $user, string $password): self
    {
        $this->user     = $user;
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getFunction(): string
    {
        return $this->function;
    }

    /**
     * @return mixed[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return RequestHeaderDto
     */
    public function getHeader(): RequestHeaderDto
    {
        return $this->header;
    }

    /**
     * @return Uri
     */
    public function getUri(): Uri
    {
        return $this->uri;
    }

}
