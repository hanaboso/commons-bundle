<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Hanaboso\CommonsBundle\Database\Traits\Document\IdTrait;
use Hanaboso\CommonsBundle\Enum\FileFormatEnum;
use Hanaboso\CommonsBundle\Enum\StorageTypeEnum;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\FileInterface;
use Hanaboso\CommonsBundle\FileStorage\FileTypes;

/**
 * Class File
 *
 * @package Hanaboso\CommonsBundle\FileStorage\Document
 */
#[ODM\Document]
class File implements FileInterface
{

    use IdTrait;

    /**
     * @var string
     */
    #[ODM\Field(type: 'string')]
    private string $filename;

    /**
     * @var string
     */
    #[ODM\Field(type: 'string', nullable: FALSE)]
    private string $fileFormat;

    /**
     * @var string
     */
    #[ODM\Field(type: 'string', nullable: FALSE)]
    private string $mimeType;

    /**
     * @var string
     */
    #[ODM\Field(type: 'string')]
    private string $fileUrl;

    /**
     * @var string
     */
    #[ODM\Field(type: 'string')]
    private string $size;

    /**
     * @var string
     */
    #[ODM\Field(type: 'string')]
    private string $storageType;

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
     * @return FileInterface
     */
    public function setFilename(string $filename): FileInterface
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileFormat(): string
    {
        return $this->fileFormat;
    }

    /**
     * @param string $format
     *
     * @return FileInterface
     * @throws FileStorageException
     */
    public function setFileFormat(string $format): FileInterface
    {
        if (!FileFormatEnum::tryFrom($format)) {
            throw new FileStorageException(
                sprintf('Given file format [%s] is not a valid option.', $format),
                FileStorageException::INVALID_FILE_FORMAT,
            );
        }
        $this->mimeType   = FileTypes::fromExtension($format);
        $this->fileFormat = $format;

        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getFileUrl(): string
    {
        return $this->fileUrl;
    }

    /**
     * @param string $url
     *
     * @return FileInterface
     */
    public function setFileUrl(string $url): FileInterface
    {
        $this->fileUrl = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @param string $size
     *
     * @return FileInterface
     */
    public function setSize(string $size): FileInterface
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return string
     */
    public function getStorageType(): string
    {
        return $this->storageType;
    }

    /**
     * @param string $type
     *
     * @return FileInterface
     * @throws FileStorageException
     */
    public function setStorageType(string $type): FileInterface
    {
        if (!StorageTypeEnum::tryFrom($type)) {
            throw new FileStorageException(
                sprintf('Given storage type [%s] is not a valid option.', $type),
                FileStorageException::INVALID_STORAGE_TYPE,
            );
        }
        $this->storageType = $type;

        return $this;
    }

}
