<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

/**
 * Class TypeEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
enum TypeEnum: string
{

    case API             = 'api';
    case BATCH           = 'batch';
    case BATCH_CONNECTOR = 'batch_connector';
    case CONNECTOR       = 'connector';
    case CRON            = 'cron';
    case CUSTOM          = 'custom';
    case DEBUG           = 'debug';
    case EMAIL           = 'email';
    case FTP             = 'ftp';
    case MAPPER          = 'mapper';
    case RESEQUENCER     = 'resequencer';
    case SIGNAL          = 'signal';
    case SPLITTER        = 'splitter';
    case TABLE_PARSER    = 'table_parser';
    case WEBHOOK         = 'webhook';
    case XML_PARSER      = 'xml_parser';
    case START           = 'start';
    case GATEWAY         = 'gateway';
    case USER            = 'user';

}
