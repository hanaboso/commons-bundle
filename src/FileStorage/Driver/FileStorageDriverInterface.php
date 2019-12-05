<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage\Driver;

use Hanaboso\CommonsBundle\FileStorage\Dto\FileInfoDto;

/**
 * Interface FileStorageDriverInterface
 *
 * @package Hanaboso\CommonsBundle\FileStorage\Driver
 */
interface FileStorageDriverInterface
{

    /**
     * @param string      $content
     * @param string|null $filename
     *
     * @return FileInfoDto
     */
    public function save(string $content, ?string $filename = NULL): FileInfoDto;

    /**
     * @param string $fileUrl
     */
    public function delete(string $fileUrl): void;

    /**
     * @param string $fileUrl
     *
     * @return string
     */
    public function get(string $fileUrl): string;

}
