<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage\Driver;

use Hanaboso\CommonsBundle\Enum\StorageTypeEnum;
use Hanaboso\CommonsBundle\Exception\FileStorageException;

/**
 * Class FileStorageDriverLocator
 *
 * @package Hanaboso\CommonsBundle\FileStorage\Driver
 */
final class FileStorageDriverLocator
{

    /**
     * FileStorageDriverLocator constructor.
     *
     * @param FileStorageDriverInterface $persistent
     * @param FileStorageDriverInterface $temporary
     * @param FileStorageDriverInterface $public
     */
    function __construct(
        private FileStorageDriverInterface $persistent,
        private FileStorageDriverInterface $temporary,
        private FileStorageDriverInterface $public,
    )
    {
    }

    /**
     * @param string $type
     *
     * @return FileStorageDriverInterface
     * @throws FileStorageException
     */
    public function get(string $type): FileStorageDriverInterface
    {
        return match ($type) {
            StorageTypeEnum::PERSISTENT => $this->persistent,
            StorageTypeEnum::TEMPORARY => $this->temporary,
            StorageTypeEnum::PUBLIC => $this->public,
            default => throw new FileStorageException(
                sprintf('Given storage type [%s] is not a valid option.', $type),
                FileStorageException::INVALID_STORAGE_TYPE,
            ),
        };
    }

}
