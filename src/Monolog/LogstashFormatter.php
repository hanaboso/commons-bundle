<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Monolog;

use Monolog\Formatter\NormalizerFormatter;
use Monolog\LogRecord;
use SoapFault;
use Throwable;

/**
 * Class LogstashFormatter
 *
 * @package Hanaboso\CommonsBundle\Monolog
 */
final class LogstashFormatter extends NormalizerFormatter
{

    /**
     * LogstashFormatter constructor.
     *
     * @param string $serviceType
     */
    public function __construct(protected string $serviceType = '')
    {
        // logstash requires a ISO 8601 format date with optional millisecond precision.
        parent::__construct('Y-m-d\TH:i:s.uP');
    }

    /**
     * @param LogRecord $record
     *
     * @return string
     */
    public function format(LogRecord $record): string
    {
        $record = parent::format($record);

        $message              = [];
        $message['timestamp'] = round(microtime(TRUE) * 1_000);
        $message['hostname']  = gethostname();
        $message['type']      = $this->serviceType;

        if ($this->serviceType === '') {
            $message['type'] = $record['channel'] ?? '';
        }

        if (isset($record['message'])) {
            $message['message'] = $record['message'];
        }

        if (isset($record['channel'])) {
            $message['channel'] = $record['channel'];
        }

        if (isset($record['level_name'])) {
            $message['severity'] = $record['level_name'];
        }

        if (isset($record['context']['exception'])) {
            $message['stacktrace'] = $record['context']['exception'];
            unset($record['context']['exception']);
        }

        if (isset($record['context']['correlation_id'])) {
            $message['correlation_id'] = $record['context']['correlation_id'];
            unset($record['context']['correlation_id']);
        }

        if (isset($record['context']['node_id'])) {
            $message['node_id'] = $record['context']['node_id'];
            unset($record['context']['node_id']);
        }

        if (isset($record['context']['node_name'])) {
            $message['node_name'] = $record['context']['node_name'];
            unset($record['context']['node_name']);
        }

        if (isset($record['context']['topology_id'])) {
            $message['topology_id'] = $record['context']['topology_id'];
            unset($record['context']['topology_id']);
        }

        if (isset($record['context']['is_for_ui'])) {
            $message['is_for_ui'] = $record['context']['is_for_ui'];
            unset($record['context']['is_for_ui']);
        }

        if (isset($record['context']['topology_name'])) {
            $message['topology_name'] = $record['context']['topology_name'];
            unset($record['context']['topology_name']);
        }

        if (!empty($record['context'])) {
            foreach ($record['context'] as $key => $val) {
                $message[$key] = $val;
            }
        }

        return sprintf('%s%s', $this->toJson($message), PHP_EOL);
    }

    /**
     * @param Throwable $e
     * @param int       $depth
     *
     * @return mixed[]
     */
    protected function normalizeException(Throwable $e, int $depth = 0): array
    {
        $depth;

        $data = [
            'class'   => $e::class,
            'message' => $e->getMessage(),
            'code'    => $e->getCode(),
            'file'    => sprintf('%s:%s', $e->getFile(), $e->getLine()),
        ];

        if ($e instanceof SoapFault) {
            if ($e->faultcode) {
                $data['faultcode'] = $e->faultcode;
            }

            if (isset($e->faultactor)) {
                $data['faultactor'] = $e->faultactor;
            }

            if (isset($e->detail)) {
                $data['detail'] = $e->detail;
            }
        }

        $data['trace'] = $this->toJson($e->getTraceAsString());

        if ($e->getPrevious()) {
            $previous         = $e->getPrevious();
            $data['previous'] = $this->normalizeException($previous);
        }

        return $data;
    }

}
