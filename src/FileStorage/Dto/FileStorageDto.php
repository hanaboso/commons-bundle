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
     * @var string
     */
    private string $content;

    /**
     * @var FileInterface
     */
    private FileInterface $file;

    /**
     * FileStorageDto constructor.
     *
     * @param FileInterface $file
     * @param string        $content
     */
    function __construct(FileInterface $file, string $content)
    {
        $this->file    = $file;
        $this->content = $content;
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
