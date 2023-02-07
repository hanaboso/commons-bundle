<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

/**
 * Class FileFormatEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
enum FileFormatEnum: string
{

    case XML  = 'xml';
    case JSON = 'json';
    case CSV  = 'csv';
    case XLS  = 'xls';
    case XLSX = 'xlsx';
    case ODS  = 'ods';
    case PDF  = 'pdf';

}
