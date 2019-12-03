<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Hanaboso\CommonsBundle\Database\Traits\Document\IdTrait;
use Hanaboso\CommonsBundle\Enum\FileFormatEnum;
use Hanaboso\CommonsBundle\Enum\StorageTypeEnum;
use Hanaboso\CommonsBundle\Exception\EnumException;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Entity\FileInterface;
use Hanaboso\CommonsBundle\FileStorage\FileTypes;

/**
 * Class File
 *
 * @package Hanaboso\CommonsBundle\FileStorage\Document
 *
 * @ODM\Document
 */
class File implements FileInterface
{

    use IdTrait;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    private string $filename;

    /**
     * @var string
     *
     * @ODM\Field(type="string", nullable=false)
     */
    private string $fileFormat;

    /**
     * @var string
     *
     * @ODM\Field(type="string", nullable=false)
     */
    private string $mimeType;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    private string $fileUrl;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    private string $size;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
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
     * @param string $fileFormat
     *
     * @return FileInterface
     * @throws FileStorageException
     * @throws EnumException
     */
    public function setFileFormat(string $fileFormat): FileInterface
    {
        if (!FileFormatEnum::isValid($fileFormat)) {
            throw new FileStorageException(
                sprintf('Given file format [%s] is not a valid option.', $fileFormat),
                FileStorageException::INVALID_FILE_FORMAT
            );
        }

        $this->mimeType   = FileTypes::fromExtension($fileFormat);
        $this->fileFormat = $fileFormat;

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
     * @param string $fileUrl
     *
     * @return FileInterface
     */
    public function setFileUrl(string $fileUrl): FileInterface
    {
        $this->fileUrl = $fileUrl;

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
     * @param string $storageType
     *
     * @return FileInterface
     * @throws FileStorageException
     * @throws EnumException
     */
    public function setStorageType(string $storageType): FileInterface
    {
        if (!StorageTypeEnum::isValid($storageType)) {
            throw new FileStorageException(
                sprintf('Given storage type [%s] is not a valid option.', $storageType),
                FileStorageException::INVALID_STORAGE_TYPE
            );
        }
        $this->storageType = $storageType;

        return $this;
    }

}
