<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Process;

use stdClass;

/**
 * Class BatchMessageDto
 *
 * @package Hanaboso\CommonsBundle\Process
 */
final class BatchMessageDto
{

    /**
     * BatchMessageDto constructor.
     *
     * @param string  $body
     * @param mixed[] $headers
     */
    public function __construct(private readonly string $body = '', private readonly array $headers = [])
    {
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
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'body'    => $this->body,
            'headers' => $this->headers ?: new stdClass(),
        ];
    }

}
