<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Process;

use DateTime;
use Hanaboso\Utils\Exception\PipesFrameworkException;
use Hanaboso\Utils\String\Json;
use Hanaboso\Utils\System\PipesHeaders;

/**
 * Class ProcessDto
 *
 * @package Hanaboso\CommonsBundle\Process
 */
final class ProcessDto
{

    public const SUCCESS = 0;

    // NON_STANDARD: 1000+
    public const REPEAT                  = 1_001;
    public const FORWARD_TO_TARGET_QUEUE = 1_002;
    public const DO_NOT_CONTINUE         = 1_003;
    public const SPLITTER_BATCH_END      = 1_005;
    public const LIMIT_EXCEEDED          = 1_004;
    public const STOP_AND_FAILED         = 1_006;

    // BATCH
    public const BATCH_CURSOR_WITH_FOLLOWERS = 1_010;
    public const BATCH_CURSOR_ONLY           = 1_011;

    // MESSAGE ERRORS: 2000+
    public const UNKNOWN_ERROR   = 2_001;
    public const INVALID_HEADERS = 2_002;
    public const INVALID_CONTENT = 2_003;

    /**
     * @var string
     */
    private string $data;

    /**
     * @var mixed[]
     */
    private array $headers;

    /**
     * @var bool
     */
    private bool $free;

    /**
     * ProcessDto constructor.
     */
    public function __construct()
    {
        $this->data    = '{}';
        $this->headers = [];
        $this->free    = TRUE;
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
    public function getJsonData(): array
    {
        return Json::decode($this->data);
    }

    /**
     * @param mixed[] $body
     */
    public function setJsonData(array $body): void
    {
        $this->data = Json::encode($body);
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
        $this->headers[$key] = str_replace(["\n", "\r"], ' ', $value);

        return $this;
    }

    /**
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getHeader(string $key, $default = NULL): mixed
    {
        return $this->headers[$key] ?? $default;
    }

    /**
     * @param string $key
     *
     * @return ProcessDto
     */
    public function deleteHeader(string $key): ProcessDto
    {
        if (isset($this->headers[$key])) {
            unset($this->headers[$key]);
        }

        return $this;
    }

    /**
     *
     */
    public function deleteHeaders(): void
    {
        $this->headers = [];
    }

    /**
     * @return bool
     */
    public function getFree(): bool
    {
        return $this->free;
    }

    /**
     * @param bool $free
     */
    public function setFree(bool $free): void
    {
        if ($free) {
            $this->data    = '';
            $this->headers = [];
        }
        $this->free = $free;
    }

    /**
     * @param string|null $message
     *
     * @return ProcessDto
     */
    public function setSuccessProcess(?string $message = NULL): ProcessDto
    {
        $this->setStatusHeader(self::SUCCESS, $message);

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
     * @param string $reason
     */
    public function setLimitExceeded(string $reason): void
    {
        $this->setStatusHeader(self::LIMIT_EXCEEDED, $reason);
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
        ?string $message = NULL,
    ): ProcessDto
    {
        if ($interval < 1) {
            throw new PipesFrameworkException(
                'Value inverval is obligatory and cant be NULL',
                PipesFrameworkException::WRONG_VALUE,
            );
        }
        if ($maxHops < 1) {
            throw new PipesFrameworkException(
                'Value maxHops is obligatory and cant be NULL',
                PipesFrameworkException::WRONG_VALUE,
            );
        }

        $this->setStatusHeader(self::REPEAT, $message);

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
     * @return ProcessDto
     */
    public function removeRepeater(): ProcessDto
    {
        $this->setStatusHeader(self::SUCCESS, NULL);
        $this->deleteHeader(PipesHeaders::createKey(PipesHeaders::REPEAT_INTERVAL));
        $this->deleteHeader(PipesHeaders::createKey(PipesHeaders::REPEAT_HOPS));
        $this->deleteHeader(PipesHeaders::createKey(PipesHeaders::REPEAT_MAX_HOPS));
        $this->deleteHeader(PipesHeaders::createKey(PipesHeaders::REPEAT_QUEUE));

        return $this;
    }

    /**
     * @param string        $key
     * @param int           $time
     * @param int           $value
     * @param DateTime|null $lastUpdate
     *
     * @return ProcessDto
     */
    public function setLimiter(string $key, int $time, int $value, ?DateTime $lastUpdate = NULL): ProcessDto
    {
        $this->addHeader(PipesHeaders::createKey(PipesHeaders::LIMIT_KEY), self::decorateLimitKey($key));
        $this->addHeader(PipesHeaders::createKey(PipesHeaders::LIMIT_TIME), (string) $time);
        $this->addHeader(PipesHeaders::createKey(PipesHeaders::LIMIT_VALUE), (string) $value);

        if ($lastUpdate) {
            $this->addHeader(
                PipesHeaders::createKey(PipesHeaders::LIMIT_LAST_UPDATE),
                (string) $lastUpdate->getTimestamp(),
            );
        }

        return $this;
    }

    /**
     * @return ProcessDto
     */
    public function removeLimiter(): ProcessDto
    {
        $this->deleteHeader(PipesHeaders::createKey(PipesHeaders::LIMIT_KEY));
        $this->deleteHeader(PipesHeaders::createKey(PipesHeaders::LIMIT_TIME));
        $this->deleteHeader(PipesHeaders::createKey(PipesHeaders::LIMIT_VALUE));
        $this->deleteHeader(PipesHeaders::createKey(PipesHeaders::LIMIT_LAST_UPDATE));

        return $this;
    }

    /**
     * @param int $code
     *
     * @return bool
     */
    public function isSuccessResultCode(int $code): bool
    {
        return in_array($code, [
            self::SUCCESS,
            self::REPEAT,
            self::FORWARD_TO_TARGET_QUEUE,
            self::DO_NOT_CONTINUE,
            self::SPLITTER_BATCH_END,
            self::BATCH_CURSOR_WITH_FOLLOWERS,
            self::BATCH_CURSOR_ONLY,
        ], TRUE);
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
        if (!in_array($value, [self::DO_NOT_CONTINUE, self::SPLITTER_BATCH_END, self::STOP_AND_FAILED], TRUE)) {
            throw new PipesFrameworkException(
                'Value does not match with the required one',
                PipesFrameworkException::WRONG_VALUE,
            );
        }
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private static function decorateLimitKey(string $key): string
    {
        $newKey = $key;
        if (!str_contains($key, '|')) {
            $newKey = sprintf('%s|', $key);
        }

        return $newKey;
    }

}
