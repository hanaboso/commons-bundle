<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Metrics\Impl;

use Hanaboso\CommonsBundle\Metrics\MetricsSenderInterface;
use Hanaboso\CommonsBundle\Transport\Udp\UDPSender;
use Hanaboso\Utils\Exception\DateTimeException;
use InvalidArgumentException;

/**
 * Class InfluxDbSender
 *
 * @package Hanaboso\CommonsBundle\Metrics\Impl
 */
final class InfluxDbSender implements MetricsSenderInterface
{

    /**
     * InfluxDbSender constructor.
     *
     * @param UDPSender $sender
     * @param string    $host
     * @param string    $measurement
     */
    public function __construct(private UDPSender $sender, private string $host, private string $measurement)
    {
    }

    /**
     * @param mixed[] $fields
     * @param mixed[] $tags
     *
     * @return bool
     * @throws DateTimeException
     */
    public function send(array $fields, array $tags = []): bool
    {
        return $this->sender->send($this->host, $this->createMessage($fields, $tags));
    }

    /**
     * @param mixed[] $fields
     * @param mixed[] $tags
     *
     * @return string
     */
    public function createMessage(array $fields, array $tags = []): string
    {
        if (empty($fields)) {
            throw new InvalidArgumentException('The fields must not be empty.');
        }

        $nanoTimestamp = sprintf('%s%s', round(microtime(TRUE) * 1_000), '000000');

        return sprintf(
            '%s%s%s %s %s',
            $this->measurement,
            empty($tags) ? ' ' : ',',
            $this->join($this->prepareTags($tags)),
            $this->join($this->prepareFields($fields)),
            $nanoTimestamp
        );
    }

    /**
     * @param mixed[] $items
     *
     * @return string
     */
    private function join(array $items): string
    {
        $result = '';

        if (empty($items)) {
            return $result;
        }

        foreach ($items as $key => $value) {
            $result .= sprintf('%s=%s,', $key, $value);
        }

        return substr($result, 0, -1);
    }

    /**
     * @param mixed[] $tags
     *
     * @return mixed[]
     */
    private function prepareTags(array $tags): array
    {
        foreach ($tags as &$tag) {
            if ($tag === '') {
                $tag = '""';
            } else if (is_bool($tag)) {
                $tag = ($tag ? 'true' : 'false');
            } else if (is_null($tag)) {
                $tag = 'null';
            }
        }

        return $tags;
    }

    /**
     * Change values by InfluxDB protocol
     *
     * @param mixed[] $fields
     *
     * @return mixed[]
     */
    private function prepareFields(array $fields): array
    {
        foreach ($fields as &$field) {
            if (is_integer($field)) {
                $field = sprintf('%d', $field);
            } else if (is_string($field)) {
                $field = $this->escapeFieldValue($field);
            } else if (is_bool($field)) {
                $field = ($field ? 'true' : 'false');
            } else if (is_null($field)) {
                $field = $this->escapeFieldValue('null');
            }
        }

        return $fields;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function escapeFieldValue(string $value): string
    {
        $escapedValue = str_replace('"', '\"', $value);

        return sprintf('"%s"', $escapedValue);
    }

}
