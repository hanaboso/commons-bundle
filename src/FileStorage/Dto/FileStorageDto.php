<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage\Dto;

use Hanaboso\CommonsBundle\FileStorage\FileInterface;

/**
 * Class FileStorageDto
 *
 * @package Hanaboso\CommonsBundle\FileStorage\Dto
 */
final class FileStorageDto
{

    /**
     * FileStorageDto constructor.
     *
     * @param FileInterface $file
     * @param string        $content
     */
    function __construct(private FileInterface $file, private string $content)
    {
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return FileInterface
     */
    public function getFile(): FileInterface
    {
        return $this->file;
    }

}
