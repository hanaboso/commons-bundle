<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Process;

/**
 * Class ProcessDto
 *
 * @package Hanaboso\CommonsBundle\Process
 */

use Exception;
use Hanaboso\CommonsBundle\Utils\PipesHeaders;

/**
 * Class ProcessDto
 *
 * @package Hanaboso\CommonsBundle\Process
 */
final class ProcessDto
{

    /**
     * @var string
     */
    private $data = '{}';

    /**
     * @var array
     */
    private $headers = [];

    private const REPEAT             = 1001;
    private const DO_NOT_CONTINUE    = 1003;
    private const SPLITTER_BATCH_END = 1005;
    private const STOP_AND_FAILED    = 1006;

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     *
     * @return ProcessDto
     */
    public function setData(string $data): ProcessDto
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     *
     * @return ProcessDto
     */
    public function setHeaders(array $headers): ProcessDto
    {
        $this->headers = PipesHeaders::clear($headers);

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return ProcessDto
     */
    public function addHeader(string $key, string $value): ProcessDto
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * @param string     $key
     * @param null|mixed $default
     *
     * @return null|mixed
     */
    public function getHeader(string $key, $default = NULL)
    {
        return $this->headers[$key] ?? $default;
    }

    /**
     * @param string $value
     *
     * @return ProcessDto
     * @throws Exception
     */
    public function setStopProcess(int $value = self::DO_NOT_CONTINUE): void
    {

        $this->validateStatus($value);
        $pipesHeaders = new PipesHeaders();
        $key          = $pipesHeaders->createKey(PipesHeaders::RESULT_CODE);

        $this->addHeader($key, (string) $value);

    }

    public function setRepeater(int $interval = self::REPEAT, int $max_hops, ?int $repeat_hops = NULL,
                                string $queue = ''): void
    {
        $pipesHeaders = new PipesHeaders();

        $key_interval = $pipesHeaders->createKey(PipesHeaders::REPEAT_INTERVAL);
        $this->addHeader($key_interval, (string) $interval);

        if ($max_hops === NULL) {
            throw new Exception('Value is obligatory and cant be NULL');
        }
        $key_max_hops = $pipesHeaders->createKey(PipesHeaders::REPEAT_MAX_HOPS);
        $this->addHeader($key_max_hops, (string) $max_hops);

        if ($repeat_hops !== NULL) {
            $key_repeat_hops = $pipesHeaders->createKey(PipesHeaders::REPEAT_HOPS);
            $this->addHeader($key_repeat_hops, (string) $repeat_hops);
        }

        if ($queue !== '') {
            $key_queue = $pipesHeaders->createKey(PipesHeaders::REPEAT_QUEUE);
            $this->addHeader($key_queue, $queue);
        }

    }

    private function validateStatus($value): void
    {

        if (!in_array($value, [self::DO_NOT_CONTINUE, self::SPLITTER_BATCH_END, self::STOP_AND_FAILED])) {

            throw new Exception('Value does not match with the required one');
        }
    }

}
