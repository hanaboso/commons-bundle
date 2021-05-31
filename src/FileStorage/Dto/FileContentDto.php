<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage\Dto;

use Hanaboso\CommonsBundle\Enum\FileFormatEnum;
use Hanaboso\CommonsBundle\Enum\StorageTypeEnum;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\Utils\Exception\EnumException;

/**
 * Class FileContentDto
 *
 * @package Hanaboso\CommonsBundle\FileStorage\Dto
 */
final class FileContentDto
{

    /**
     * @var string
     */
    private string $type;

    /**
     * FileContentDto constructor.
     *
     * @param string      $content
     * @param string      $format
     * @param string|null $filename
     */
    function __construct(private string $content, private string $format, private ?string $filename = NULL)
    {
        $this->type = StorageTypeEnum::PERSISTENT;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     *
     * @return FileContentDto
     */
    public function setFilename(string $filename): FileContentDto
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getStorageType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return FileContentDto
     * @throws FileStorageException
     */
    public function setStorageType(string $type): FileContentDto
    {
        try {
            StorageTypeEnum::isValid($type);
        } catch (EnumException) {
            throw new FileStorageException(
                sprintf('Given storage type [%s] is not a valid option.', $type),
                FileStorageException::INVALID_STORAGE_TYPE,
            );
        }
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return FileContentDto
     */
    public function setContent(string $content): FileContentDto
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     *
     * @return FileContentDto
     * @throws FileStorageException
     */
    public function setFormat(string $format): FileContentDto
    {
        try {
            FileFormatEnum::isValid($format);
        } catch (EnumException) {
            throw new FileStorageException(
                sprintf('Given file format [%s] is not a valid option.', $format),
                FileStorageException::INVALID_FILE_FORMAT,
            );
        }
        $this->format = $format;

        return $this;
    }

}
