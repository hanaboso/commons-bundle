<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Ftp\Exception;

use Hanaboso\Utils\Exception\PipesFrameworkExceptionAbstract;

/**
 * Class FtpException
 *
 * @package Hanaboso\CommonsBundle\Transport\Ftp\Exception
 */
final class FtpException extends PipesFrameworkExceptionAbstract
{

    public const int CONNECTION_FAILED          = self::OFFSET + 1;
    public const int CONNECTION_CLOSE_FAILED    = self::OFFSET + 2;
    public const int LOGIN_FAILED               = self::OFFSET + 3;
    public const int FILE_UPLOAD_FAILED         = self::OFFSET + 4;
    public const int FILE_DOWNLOAD_FAILED       = self::OFFSET + 5;
    public const int CONNECTION_NOT_ESTABLISHED = self::OFFSET + 6;
    public const int UNABLE_TO_CREATE_DIR       = self::OFFSET + 7;
    public const int FILES_LISTING_FAILED       = self::OFFSET + 8;
    public const int UNKNOWN_ADAPTER_TYPE       = self::OFFSET + 9;
    public const int CREATING_SUBSYSTEM_FAILED  = self::OFFSET + 10;

    protected const int OFFSET = 2_500;

}
