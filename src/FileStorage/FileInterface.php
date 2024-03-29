<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage;

/**
 * Interface FileInterface
 *
 * @package Hanaboso\CommonsBundle\FileStorage
 */
interface FileInterface
{

    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getFilename(): string;

    /**
     * @param string $filename
     *
     * @return FileInterface
     */
    public function setFilename(string $filename): self;

    /**
     * @return string
     */
    public function getFileFormat(): string;

    /**
     * @param string $format
     *
     * @return FileInterface
     */
    public function setFileFormat(string $format): self;

    /**
     * @return string
     */
    public function getFileUrl(): string;

    /**
     * @param string $url
     *
     * @return FileInterface
     */
    public function setFileUrl(string $url): self;

    /**
     * @return string
     */
    public function getSize(): string;

    /**
     * @param string $size
     *
     * @return FileInterface
     */
    public function setSize(string $size): self;

    /**
     * @return string
     */
    public function getStorageType(): string;

    /**
     * @param string $type
     *
     * @return FileInterface
     */
    public function setStorageType(string $type): self;

}
