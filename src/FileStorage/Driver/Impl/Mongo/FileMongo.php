<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo;

use Doctrine\MongoDB\GridFSFile;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Hanaboso\CommonsBundle\Database\Traits\Document\IdTrait;

/**
 * Class FileMongo
 *
 * @package Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo
 *
 * @ODM\Document()
 */
class FileMongo
{

    use IdTrait;

    /**
     * @var GridFSFile
     *
     * @ODM\File
     */
    private $content;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    private $filename;

    /**
     * @return GridFSFile
     */
    public function getContent(): GridFSFile
    {
        return $this->content;
    }

    /**
     * @param GridFSFile $file
     *
     * @return FileMongo
     */
    public function setContent($file): FileMongo
    {
        $this->content = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     *
     * @return FileMongo
     */
    public function setFilename(string $filename): FileMongo
    {
        $this->filename = $filename;

        return $this;
    }

}