<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

/**
 * Class MetricsEnum
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
enum MetricsEnum: string
{

    // Tags
    case HOST           = 'host';
    case URI            = 'uri';
    case CORRELATION_ID = 'correlation_id';
    case TOPOLOGY_ID    = 'topology_id';
    case NODE_ID        = 'node_id';
    case USER_ID        = 'user_id';
    case APPLICATION_ID = 'application_id';

    // Fields
    case REQUEST_TOTAL_DURATION      = 'fpm_request_total_duration';
    case CPU_USER_TIME               = 'fpm_cpu_user_time';
    case CPU_KERNEL_TIME             = 'fpm_cpu_kernel_time';
    case REQUEST_TOTAL_DURATION_SENT = 'sent_request_total_duration';

}
