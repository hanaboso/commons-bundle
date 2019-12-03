<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Traits;

/**
 * Trait UrlBuilderTrait
 *
 * @package Hanaboso\CommonsBundle\Traits
 */
trait UrlBuilderTrait
{

    /**
     * @var string
     */
    protected string $host;

    /**
     * @param string $part
     * @param string ...$nestedPart
     *
     * @return string
     */
    protected function getUrl(string $part, string ...$nestedPart): string
    {
        return sprintf('%s/%s', rtrim($this->host, '/'), sprintf($part, ...$nestedPart));
    }

}
