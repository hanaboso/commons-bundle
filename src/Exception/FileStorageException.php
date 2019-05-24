<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Exception;

/**
 * Class FileStorageException
 *
 * @package Hanaboso\CommonsBundle\Exception
 */
final class FileStorageException extends PipesFrameworkExceptionAbstract
{

    protected const OFFSET = 1500;

    public const FILE_NOT_FOUND       = self::OFFSET + 1;
    public const INVALID_STORAGE_TYPE = self::OFFSET + 2;
    public const INVALID_FILE_FORMAT  = self::OFFSET + 3;
    public const INVALID_MIMIC_FORMAT = self::OFFSET + 4;

}
