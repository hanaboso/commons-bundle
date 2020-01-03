<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Ftp\Exception;

use Hanaboso\CommonsBundle\Exception\PipesFrameworkExceptionAbstract;

/**
 * Class FtpException
 *
 * @package Hanaboso\CommonsBundle\Transport\Ftp\Exception
 */
class FtpException extends PipesFrameworkExceptionAbstract
{

    protected const OFFSET = 2_500;

    public const CONNECTION_FAILED          = self::OFFSET + 1;
    public const CONNECTION_CLOSE_FAILED    = self::OFFSET + 2;
    public const LOGIN_FAILED               = self::OFFSET + 3;
    public const FILE_UPLOAD_FAILED         = self::OFFSET + 4;
    public const FILE_DOWNLOAD_FAILED       = self::OFFSET + 5;
    public const CONNECTION_NOT_ESTABLISHED = self::OFFSET + 6;
    public const UNABLE_TO_CREATE_DIR       = self::OFFSET + 7;
    public const FILES_LISTING_FAILED       = self::OFFSET + 8;
    public const UNKNOWN_ADAPTER_TYPE       = self::OFFSET + 9;
    public const CREATING_SUBSYSTEM_FAILED  = self::OFFSET + 10;

}