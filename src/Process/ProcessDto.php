<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Process;

use Hanaboso\Utils\Exception\PipesFrameworkException;
use Hanaboso\Utils\System\PipesHeaders;

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
    private string $data;

    /**
     * @var mixed[]
     */
    private array $headers;

    public const OK                 = 0;
    public const REPEAT             = 1_001;
    public const DO_NOT_CONTINUE    = 1_003;
    public const SPLITTER_BATCH_END = 1_005;
    public const STOP_AND_FAILED    = 1_006;

    /**
     * ProcessDto constructor.
     */
    public function __construct()
    {
        $this->data    = '{}';
        $this->headers = [];
    }

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
     * @return mixed[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param mixed[] $headers
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
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function getHeader(string $key, $default = NULL)
    {
        return $this->headers[$key] ?? $default;
    }

    /**
     * @param string|null $message
     *
     * @return ProcessDto
     */
    public function setSuccessProcess(?string $message = NULL): ProcessDto
    {
        $this->setStatusHeader(self::OK, $message);

        return $this;
    }

    /**
     * @param int         $value
     * @param string|null $message
     *
     * @return ProcessDto
     * @throws PipesFrameworkException
     */
    public function setStopProcess(int $value = self::DO_NOT_CONTINUE, ?string $message = NULL): ProcessDto
    {
        $this->validateStatus($value);
        $this->setStatusHeader($value, $message);

        return $this;
    }

    /**
     * @param int         $interval
     * @param int         $maxHops
     * @param int|null    $repeatHops
     * @param string      $queue
     * @param string|null $message
     *
     * @return ProcessDto
     * @throws PipesFrameworkException
     */
    public function setRepeater(
        int $interval,
        int $maxHops,
        ?int $repeatHops = NULL,
        string $queue = '',
        ?string $message = NULL
    ): ProcessDto
    {
        if ($interval < 1) {
            throw new PipesFrameworkException(
                'Value inverval is obligatory and cant be NULL',
                PipesFrameworkException::WRONG_VALUE
            );
        }
        if ($maxHops < 1) {
            throw new PipesFrameworkException(
                'Value maxHops is obligatory and cant be NULL',
                PipesFrameworkException::WRONG_VALUE
            );
        }

        $this->setStatusHeader(self::REPEAT, $message);
        $keyRepeat = PipesHeaders::createKey(PipesHeaders::RESULT_CODE);
        $this->addHeader($keyRepeat, (string) self::REPEAT);

        $keyInterval = PipesHeaders::createKey(PipesHeaders::REPEAT_INTERVAL);
        $this->addHeader($keyInterval, (string) $interval);

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

        return $this;
    }

    /**
     * ------------------------------------- HELPERS -----------------------------------------------
     */

    /**
     * @param int         $value
     * @param string|null $message
     */
    private function setStatusHeader(int $value, ?string $message): void
    {
        $key = PipesHeaders::createKey(PipesHeaders::RESULT_CODE);

        if ($message) {
            $this->addHeader(PipesHeaders::createKey(PipesHeaders::RESULT_MESSAGE), $message);
        }
        $this->addHeader($key, (string) $value);
    }

    /**
     * @param int $value
     *
     * @throws PipesFrameworkException
     */
    private function validateStatus(int $value): void
    {
        if (!in_array($value, [self::DO_NOT_CONTINUE, self::SPLITTER_BATCH_END, self::STOP_AND_FAILED])) {
            throw new PipesFrameworkException(
                'Value does not match with the required one',
                PipesFrameworkException::WRONG_VALUE
            );
        }
    }

}
