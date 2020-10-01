<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

use Hanaboso\Utils\Enum\EnumAbstract;

/**
 * Class MetricsEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
final class MetricsEnum extends EnumAbstract
{

    // Tags
    public const HOST           = 'host';
    public const URI            = 'uri';
    public const CORRELATION_ID = 'correlation_id';
    public const TOPOLOGY_ID    = 'topology_id';
    public const NODE_ID        = 'node_id';
    public const USER_ID        = 'user_id';
    public const APPLICATION_ID = 'application_id';

    // Fields
    public const REQUEST_TOTAL_DURATION      = 'fpm_request_total_duration';
    public const CPU_USER_TIME               = 'fpm_cpu_user_time';
    public const CPU_KERNEL_TIME             = 'fpm_cpu_kernel_time';
    public const REQUEST_TOTAL_DURATION_SENT = 'sent_request_total_duration';

    /**
     * @var string[]
     */
    protected static array $choices = [
        // tags
        self::HOST                        => self::HOST,
        self::URI                         => self::URI,
        self::CORRELATION_ID              => self::CORRELATION_ID,
        self::TOPOLOGY_ID                 => self::TOPOLOGY_ID,
        self::USER_ID                     => self::USER_ID,
        self::APPLICATION_ID              => self::APPLICATION_ID,
        // fields
        self::REQUEST_TOTAL_DURATION      => self::REQUEST_TOTAL_DURATION,
        self::CPU_USER_TIME               => self::CPU_USER_TIME,
        self::CPU_KERNEL_TIME             => self::CPU_KERNEL_TIME,
        self::REQUEST_TOTAL_DURATION_SENT => self::REQUEST_TOTAL_DURATION_SENT,
    ];

}
