<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: david.horacek
 * Date: 8/23/17
 * Time: 8:29 AM
 */

namespace Hanaboso\CommonsBundle\FileStorage\Dto;

use Hanaboso\CommonsBundle\FileStorage\Entity\FileInterface;

/**
 * Class FileStorageDto
 *
 * @package Hanaboso\CommonsBundle\FileStorage\Dto
 */
class FileStorageDto
{

    /**
     * @var string
     */
    private $content;

    /**
     * @var FileInterface
     */
    private $file;

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