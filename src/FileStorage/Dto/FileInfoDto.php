<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage\Dto;

/**
 * Class FileInfoDto
 *
 * @package Hanaboso\CommonsBundle\FileStorage\Dto
 */
final class FileInfoDto
{

    /**
     * FileInfoDto constructor.
     *
     * @param string $url
     * @param string $size
     */
    function __construct(private string $url, private string $size)
    {
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

}
