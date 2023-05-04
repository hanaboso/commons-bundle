<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Process;

use Hanaboso\Utils\Exception\PipesFrameworkException;
use Hanaboso\Utils\String\Json;
use Hanaboso\Utils\System\PipesHeaders;

/**
 * Class ProcessDtoAbstract
 *
 * @package Hanaboso\CommonsBundle\Process
 */
abstract class ProcessDtoAbstract
{

    public const SUCCESS = 0;

    // NON_STANDARD: 1000+
    public const REPEAT                  = 1_001;
    public const FORWARD_TO_TARGET_QUEUE = 1_002;
    public const DO_NOT_CONTINUE         = 1_003;
    public const SPLITTER_BATCH_END      = 1_005;
    public const LIMIT_EXCEEDED          = 1_004;
    public const STOP_AND_FAILED         = 1_006;

    // MESSAGE ERRORS: 2000+
    public const UNKNOWN_ERROR   = 2_001;
    public const INVALID_HEADERS = 2_002;
    public const INVALID_CONTENT = 2_003;

    public const ALLOWED_RESULT_CODES = [self::STOP_AND_FAILED, self::DO_NOT_CONTINUE];

    /**
     * @var string
     */
    protected string $data;

    /**
     * @var mixed[]
     */
    protected array $headers;

    /**
     * ProcessDtoAbstract constructor.
     */
    public function __construct()
    {
        $this->data    = '';
        $this->headers = [];
    }

    /**
     * @param string $user
     *
     * @return $this
     */
    public function setUser(string $user): self {
        $this->headers[PipesHeaders::USER] = $user;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->headers[PipesHeaders::USER] ?? NULL;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @return mixed[]
     */
    public function getJsonData(): array
    {
        return Json::decode($this->data ?: '{}');
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
     * @return $this
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function addHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function removeHeader(string $key): self
    {
        if (isset($this->headers[$key])) {
            unset($this->headers[$key]);
        }

        return $this;
    }

    /**
     *
     */
    public function removeHeaders(): self
    {
        $this->headers = [];

        return $this;
    }

    /**
     * @param string      $key
     * @param string|null $defaultValue
     *
     * @return mixed
     */
    public function getHeader(string $key, ?string $defaultValue = NULL): mixed
    {
        return $this->headers[$key] ?? $defaultValue;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setSuccessProcess(string $message = 'Message has been processed successfully.'): self
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
    public function setStopProcess(int $status, string $reason): self
    {
        self::validateStatus($status);

        $this->setStatusHeader($status, $reason);

        return $this;
    }

    /**
     * @param string $reason
     *
     * @return $this
     */
    public function setLimitExceeded(string $reason): self
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
    public function setRepeater(int $interval, int $maxHops, string $reason ): self
    {
        if ($interval < 1) {
            throw new PipesFrameworkException(
                'Value interval is obligatory and can not be lower than 0',
                PipesFrameworkException::WRONG_VALUE,
            );
        }
        if ($maxHops < 1) {
            throw new PipesFrameworkException(
                'Value maxHops is obligatory and can not be lower than 0',
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
    public function removeRepeater(): self
    {
        $this->removeHeader(PipesHeaders::REPEAT_INTERVAL);
        $this->removeHeader(PipesHeaders::REPEAT_MAX_HOPS);
        $this->removeHeader(PipesHeaders::REPEAT_HOPS);
        $this->removeHeader(PipesHeaders::REPEAT_QUEUE);
        $this->removeRelatedHeaders([self::REPEAT]);

        return $this;
    }

    /**
     * @param string $key
     * @param int    $time
     * @param int    $amount
     *
     * @return $this
     */
    public function setLimiter(string $key, int $time, int $amount): self
    {
        $lk = sprintf('%s;%s;%s', self::decorateLimitKey($key), $time, $amount);
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
     * @return $this
     */
    public function setLimiterWithGroup(
        string $key,
        int $time,
        int $amount,
        string $groupKey,
        int $groupTime,
        int $groupAmount,
    ): self
    {
        $lk = sprintf(
            '%s;%s;%s;%s;%s;%s',
            self::decorateLimitKey($key),
            $time,
            $amount,
            self::decorateLimitKey($groupKey),
            $groupTime,
            $groupAmount,
        );
        $this->addHeader(PipesHeaders::LIMITER_KEY, $lk);

        return $this;
    }

    /**
     * @return $this
     */
    public function removeLimiter(): self
    {
        $this->removeHeader(PipesHeaders::LIMITER_KEY);

        return $this;
    }

    /**
     * @param mixed[] $followers
     *
     * @throws PipesFrameworkException
     */
    public function setForceFollowers(array $followers): self
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
            self::FORWARD_TO_TARGET_QUEUE,
            sprintf('Message will be force re-routed to [%s] follower(s).', $targetQueues),
        );

        return $this;
    }

    /**
     *
     */
    public function removeForceFollowers(): self
    {
        $this->removeHeader(PipesHeaders::FORCE_TARGET_QUEUE);
        $this->removeRelatedHeaders([self::FORWARD_TO_TARGET_QUEUE]);

        return $this;
    }

    /**
     * @return mixed
     */
    function getBridgeData(): mixed
    {
        return $this->data;
    }

    /**
     * ------------------------------------- HELPERS -----------------------------------------------
     */

    /**
     * @param int         $value
     * @param string|null $message
     */
    protected function setStatusHeader(int $value, ?string $message): void
    {
        if ($message) {
            $this->addHeader(PipesHeaders::RESULT_MESSAGE, preg_replace('/\r?\n|\r/', '', $message) ?? '');
        }
        $this->addHeader(PipesHeaders::RESULT_CODE, (string) $value);
    }

    /**
     * @param int[] $headerCodes
     */
    protected function removeRelatedHeaders(array $headerCodes): void
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
    protected static function decorateLimitKey(string $key): string
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
    protected static function validateStatus(int $code): void
    {
        if (!in_array($code, self::ALLOWED_RESULT_CODES, TRUE)) {
            throw new PipesFrameworkException(
                'Value does not match with the required one',
                PipesFrameworkException::WRONG_VALUE,
            );
        }
    }

}
