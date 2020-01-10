<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

use Hanaboso\Utils\Enum\EnumAbstract;

/**
 * Class FileFormatEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
final class FileFormatEnum extends EnumAbstract
{

    public const XML  = 'xml';
    public const JSON = 'json';
    public const CSV  = 'csv';
    public const XLS  = 'xls';
    public const XLSX = 'xlsx';
    public const ODS  = 'ods';
    public const PDF  = 'pdf';

    /**
     * @var string[]
     */
    protected static array $choices = [
        self::XML  => 'XML',
        self::JSON => 'JSON',
        self::CSV  => 'CSV',
        self::XLSX => 'XLSX',
        self::ODS  => 'ODS',
        self::PDF  => 'PDF',
    ];

}
