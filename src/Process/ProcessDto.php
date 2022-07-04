<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Process;

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
    public function setJsonData(array $body): ProcessDto
    {
        $this->data = Json::encode($body);

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
        $this->headers[PipesHeaders::createKey($key)] = str_replace(["\n", "\r"], ' ', $value);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return ProcessDto
     */
    public function removeHeader(string $key): ProcessDto
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
    public function removeHeaders(): ProcessDto
    {
        $this->headers = [];

        return $this;
    }

    /**
     * @param string     $key
     * @param mixed|null $defaultValue
     *
     * @return mixed
     */
    public function getHeader(string $key, $defaultValue = NULL): mixed
    {
        return $this->headers[PipesHeaders::createKey($key)] ?? $defaultValue;
    }

    /**
     * @param string $user
     *
     * @return $this
     */
    public function setUser(string $user): ProcessDto {
        $this->headers[PipesHeaders::createKey(PipesHeaders::USER)] = $user;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->headers[PipesHeaders::createKey(PipesHeaders::USER)] ?? NULL;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setSuccessProcess(string $message = 'Message has been processed successfully.'): ProcessDto
    {
        $this->setStatusHeader(self::SUCCESS, $message);

        return $this;
    }

    /**
     * @param int    $status
     * @param string $reason
     *
     * @return $this
     * @throws PipesFrameworkException
     */
    public function setStopProcess(int $status, string $reason): ProcessDto
    {
        ProcessDto::validateStatus($status);

        $this->setStatusHeader($status, $reason);

        return $this;
    }

    /**
     * @param string $reason
     *
     * @return $this
     */
    public function setLimitExceeded(string $reason): ProcessDto
    {
        $this->setStatusHeader(self::LIMIT_EXCEEDED, $reason);

        return $this;
    }

    /**
     * @param int    $interval
     * @param int    $maxHops
     * @param string $reason
     *
     * @return $this
     * @throws PipesFrameworkException
     */
    public function setRepeater(int $interval, int $maxHops, string $reason ): ProcessDto
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

        $this->setStatusHeader(self::REPEAT, $reason);

        $this->addHeader(PipesHeaders::REPEAT_INTERVAL, (string) $interval);

        $this->addHeader(PipesHeaders::REPEAT_MAX_HOPS, (string) $maxHops);

        return $this;
    }

    /**
     *
     */
    public function removeRepeater(): ProcessDto
    {
        $this->removeHeader(PipesHeaders::REPEAT_INTERVAL);
        $this->removeHeader(PipesHeaders::REPEAT_MAX_HOPS);
        $this->removeHeader(PipesHeaders::REPEAT_HOPS);
        $this->removeHeader(PipesHeaders::REPEAT_QUEUE);
        $this->removeRelatedHeaders([ProcessDto::REPEAT]);

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
     *
     * @return ProcessDto
     */
    public function setLimiterWithGroup(
        string $key,
        int $time,
        int $amount,
        string $groupKey,
        int $groupTime,
        int $groupAmount,
    ): ProcessDto
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

        return $this;
    }

    /**
     * @return ProcessDto
     */
    public function removeLimiter(): ProcessDto
    {
        $this->removeHeader(PipesHeaders::LIMITER_KEY);

        return $this;
    }

    /**
     * @param string $cursor
     * @param bool   $iterateOnly
     *
     * @return ProcessDto
     */
    public function setBatchCursor(string $cursor, bool $iterateOnly = FALSE): ProcessDto
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

        return $this;
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
     * @return $this
     */
    public function removeBatchCursor(): ProcessDto
    {
        $this->removeHeader(PipesHeaders::BATCH_CURSOR);
        $this->removeRelatedHeaders([ProcessDto::BATCH_CURSOR_ONLY, ProcessDto::BATCH_CURSOR_WITH_FOLLOWERS]);

        return $this;
    }

    /**
     * @param mixed[] $followers
     *
     * @throws PipesFrameworkException
     */
    public function setForceFollowers(array $followers): ProcessDto
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
            $workerFollowerNames = implode(',', array_column($workerFollowers, 'name'));

            throw new PipesFrameworkException(
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

        return $this;
    }

    /**
     *
     */
    public function removeForceFollowers(): ProcessDto
    {
        $this->removeHeader(PipesHeaders::FORCE_TARGET_QUEUE);
        $this->removeRelatedHeaders([ProcessDto::FORWARD_TO_TARGET_QUEUE]);

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
        if ($message) {
            $this->addHeader(PipesHeaders::RESULT_MESSAGE, preg_replace('/\r?\n|\r/', '', $message) ?? '');
        }
        $this->addHeader(PipesHeaders::RESULT_CODE, (string) $value);
    }

    /**
     * @param int[] $headerCodes
     */
    private function removeRelatedHeaders(array $headerCodes): void
    {
        if (in_array((int) $this->getHeader(PipesHeaders::RESULT_CODE, ''), $headerCodes, TRUE)) {
            $this->removeHeader(PipesHeaders::RESULT_MESSAGE);
            $this->removeHeader(PipesHeaders::RESULT_CODE);
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

    /**
     * @param int $code
     *
     * @throws PipesFrameworkException
     */
    private static function validateStatus(int $code): void
    {
        if (!in_array($code, [self::DO_NOT_CONTINUE, self::STOP_AND_FAILED], TRUE)) {
            throw new PipesFrameworkException(
                'Value does not match with the required one',
                PipesFrameworkException::WRONG_VALUE,
            );
        }
    }

}
