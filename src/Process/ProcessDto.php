<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Process;

use Error;
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
        $this->headers[PipesHeaders::createKey($key)] = str_replace(["\n", "\r"], ' ', $value);

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
        return $this->headers[PipesHeaders::createKey($key)] ?? $default;
    }

    /**
     * @param string $key
     *
     * @return ProcessDto
     */
    public function deleteHeader(string $key): ProcessDto
    {
        $key = PipesHeaders::createKey($key);
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
     * @param string|null $message
     *
     * @return ProcessDto
     */
    public function setSuccessProcess(?string $message = 'Message has been processed successfully.'): ProcessDto
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

        $this->addHeader(PipesHeaders::REPEAT_INTERVAL, (string) $interval);

        $this->addHeader(PipesHeaders::REPEAT_MAX_HOPS, (string) $maxHops);

        if ($repeatHops !== NULL) {
            $this->addHeader(PipesHeaders::REPEAT_HOPS, (string) $repeatHops);
        }

        if ($queue !== '') {
            $this->addHeader(PipesHeaders::REPEAT_QUEUE, $queue);
        }

        return $this;
    }

    /**
     * @return ProcessDto
     */
    public function removeRepeater(): ProcessDto
    {
        $this->setStatusHeader(self::SUCCESS, NULL);
        $this->deleteHeader(PipesHeaders::REPEAT_INTERVAL);
        $this->deleteHeader(PipesHeaders::REPEAT_HOPS);
        $this->deleteHeader(PipesHeaders::REPEAT_MAX_HOPS);
        $this->deleteHeader(PipesHeaders::REPEAT_QUEUE);

        return $this;
    }

    /**
     * @param string $key
     * @param int    $time
     * @param int    $amount
     *
     * @return ProcessDto
     */
    public function setLimiter(string $key, int $time, int $amount): ProcessDto
    {
        $lk = sprintf('%s;%s;%s', ProcessDto::decorateLimitKey($key), $time, $amount);
        $this->addHeader(PipesHeaders::LIMITER_KEY, $lk);

        return $this;
    }

    /**
     * @param string $key
     * @param int    $time
     * @param int    $amount
     * @param string $groupKey
     * @param int    $groupTime
     * @param int    $groupAmount
     */
    public function setLimiterWithGroup(
        string $key,
        int $time,
        int $amount,
        string $groupKey,
        int $groupTime,
        int $groupAmount,
    ): void
    {
        $lk = sprintf(
            '%s;%s;%s;%s;%s;%s',
            ProcessDto::decorateLimitKey($key),
            $time,
            $amount,
            ProcessDto::decorateLimitKey($groupKey),
            $groupTime,
            $groupAmount,
        );
        $this->addHeader(PipesHeaders::LIMITER_KEY, $lk);
    }

    /**
     * @return ProcessDto
     */
    public function removeLimiter(): ProcessDto
    {
        $this->deleteHeader(PipesHeaders::LIMITER_KEY);

        return $this;
    }

    /**
     * @param string $cursor
     * @param bool   $iterateOnly
     */
    public function setBatchCursor(string $cursor, bool $iterateOnly = FALSE): void
    {
        $this->addHeader(PipesHeaders::BATCH_CURSOR, $cursor);
        if ($iterateOnly) {
            $this->setStatusHeader(
                ProcessDto::BATCH_CURSOR_ONLY,
                sprintf('Message will be used as a iterator with cursor [%s]. No follower will be called.', $cursor),
            );
        } else {
            $this->setStatusHeader(
                ProcessDto::BATCH_CURSOR_WITH_FOLLOWERS,
                sprintf(
                    'Message will be used as a iterator with cursor [%s]. Data will be send to follower(s).',
                    $cursor,
                ),
            );
        }
    }

    /**
     * @param string $defaultValue
     *
     * @return string
     */
    public function getBatchCursor(string $defaultValue = ''): string
    {
        return (string) $this->getHeader(PipesHeaders::BATCH_CURSOR, $defaultValue);
    }

    /**
     *
     */
    public function removeBatchCursor(): void
    {
        $this->deleteHeader(PipesHeaders::BATCH_CURSOR);
        $this->removeRelatedHeaders(ProcessDto::BATCH_CURSOR_ONLY);
        $this->removeRelatedHeaders(ProcessDto::BATCH_CURSOR_WITH_FOLLOWERS);
    }

    /**
     * @param mixed[] $followers
     */
    public function setForceFollowers(array $followers): void
    {
        $workerFollowers = Json::decode(
            (string) $this->getHeader(PipesHeaders::WORKER_FOLLOWERS, '[]'),
        );
        $filtered        = array_filter(
            $workerFollowers,
            static fn(array $item) => in_array($item['name'], $followers, TRUE),
        );
        $targetQueues    = implode(',', array_column($filtered, 'id'));
        if (!$targetQueues) {
            $workerFollowerNames = implode(',', array_column($workerFollowers, 'id'));

            throw new Error(
                sprintf(
                    "Inserted follower(s) [%s] can't be reached. Available follower(s) [%s]",
                    implode(',', $followers),
                    $workerFollowerNames,
                ),
            );
        }
        $this->addHeader(PipesHeaders::FORCE_TARGET_QUEUE, $targetQueues);
        $this->setStatusHeader(
            ProcessDto::FORWARD_TO_TARGET_QUEUE,
            sprintf('Message will be force re-routed to [%s] follower(s).', $targetQueues),
        );
    }

    /**
     *
     */
    public function removeForceFollowers(): void
    {
        $this->deleteHeader(PipesHeaders::FORCE_TARGET_QUEUE);
        $this->removeRelatedHeaders(ProcessDto::FORWARD_TO_TARGET_QUEUE);
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
        ],              TRUE);
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
        if ($message) {
            $this->addHeader(PipesHeaders::RESULT_MESSAGE, $message);
        }
        $this->addHeader(PipesHeaders::RESULT_CODE, (string) $value);
    }

    /**
     * @return string
     */
    private function getStatusHeader(): string
    {
        return $this->getHeader(PipesHeaders::RESULT_CODE, '');
    }

    /**
     * @param int $headerCode
     */
    private function removeRelatedHeaders(int $headerCode): void
    {
        if ((int) $this->getStatusHeader() === $headerCode) {
            $this->deleteHeader(PipesHeaders::RESULT_MESSAGE);
            $this->deleteHeader(PipesHeaders::RESULT_CODE);
        }
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
