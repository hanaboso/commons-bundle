<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Exception;

use Hanaboso\Utils\Exception\PipesFrameworkExceptionAbstract;

/**
 * Class FileStorageException
 *
 * @package Hanaboso\CommonsBundle\Exception
 */
final class FileStorageException extends PipesFrameworkExceptionAbstract
{

    public const int FILE_NOT_FOUND       = self::OFFSET + 1;
    public const int INVALID_STORAGE_TYPE = self::OFFSET + 2;
    public const int INVALID_FILE_FORMAT  = self::OFFSET + 3;
    public const int INVALID_MIMIC_FORMAT = self::OFFSET + 4;

    protected const int OFFSET = 1_500;

}
