<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: david.horacek
 * Date: 8/22/17
 * Time: 8:41 AM
 */

namespace Hanaboso\CommonsBundle\Enum;

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

    /**
     * @var string[]
     */
    protected static $choices = [
        self::XML  => 'XML',
        self::JSON => 'JSON',
        self::CSV  => 'CSV',
        self::XLSX => 'XLSX',
        self::ODS  => 'ODS',
    ];

}