<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Process;

/**
 * Class ProcessDto
 *
 * @package Hanaboso\CommonsBundle\Process
 */

use Hanaboso\CommonsBundle\Exception\PipesFrameworkException;
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
     * @param int $value
     *
     * @throws PipesFrameworkException
     */
    public function setStopProcess(int $value = self::DO_NOT_CONTINUE): void
    {

        $this->validateStatus($value);
        $key          = PipesHeaders::createKey(PipesHeaders::RESULT_CODE);

        $this->addHeader($key, (string) $value);

    }

    /**
     * @param int      $interval
     * @param int      $maxHops
     * @param int|null $repeatHops
     * @param string   $queue
     *
     * @throws PipesFrameworkException
     */
    public function setRepeater(
        int $interval,
        int $maxHops,
        ?int $repeatHops = NULL,
        string $queue = ''
    ): void

    {
        $keyRepeat =  PipesHeaders::createKey(PipesHeaders::RESULT_CODE);
        $this->addHeader($keyRepeat, (string) self::REPEAT);

        if ($interval < 1) {
            throw new PipesFrameworkException('Value inverval is obligatory and cant be NULL',
                PipesFrameworkException::WRONG_VALUE);
        }

        $keyInterval = PipesHeaders::createKey(PipesHeaders::REPEAT_INTERVAL);
        $this->addHeader($keyInterval, (string) $interval);

        if ($maxHops < 1) {
            throw new PipesFrameworkException('Value maxHops is obligatory and cant be NULL',
                PipesFrameworkException::WRONG_VALUE);
        }

        $keyMaxHops = PipesHeaders::createKey(PipesHeaders::REPEAT_MAX_HOPS);
        $this->addHeader($keyMaxHops, (string) $maxHops);

        if ($repeatHops !== NULL) {
            $keyRepeatHops = PipesHeaders::createKey(PipesHeaders::REPEAT_HOPS);
            $this->addHeader($keyRepeatHops, (string) $repeatHops);
        }

        if ($queue !== '') {
            $keyQueue = PipesHeaders::createKey(PipesHeaders::REPEAT_QUEUE);
            $this->addHeader($keyQueue, $queue);
        }

    }

    /**
     * @param int $value
     *
     * @throws PipesFrameworkException
     */
    private function validateStatus(int $value): void
    {

        if (!in_array($value, [self::DO_NOT_CONTINUE, self::SPLITTER_BATCH_END, self::STOP_AND_FAILED])) {

            throw new PipesFrameworkException('Value does not match with the required one',
                PipesFrameworkException::WRONG_VALUE);
        }
    }

}
