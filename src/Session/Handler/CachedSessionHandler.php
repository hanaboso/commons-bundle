<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Session\Handler;

use Hanaboso\CommonsBundle\Exception\DateTimeException;
use Hanaboso\CommonsBundle\Utils\DateTimeUtils;
use SessionHandlerInterface;

/**
 * Class CachedSessionHandler
 *
 * @package Hanaboso\CommonsBundle\Session\Handler
 */
class CachedSessionHandler implements SessionHandlerInterface
{

    private const APCU_DELIMITER = '::';
    private const APCU_DATA_KEY  = 'session-data';
    private const APCU_TIME_KEY  = 'session-time';
    private const APCU_TIMEOUT   = 5;

    /**
     * @var SessionHandlerInterface
     */
    private SessionHandlerInterface $handler;

    /**
     * @var int
     */
    private int $timeout;

    /**
     * CachedSessionHandler constructor.
     *
     * @param SessionHandlerInterface $handler
     */
    public function __construct(SessionHandlerInterface $handler)
    {
        $this->handler = $handler;
        $this->timeout = self::APCU_TIMEOUT;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    /**
     * @param string $sessionId
     *
     * @return string
     * @throws DateTimeException
     */
    public function read($sessionId): string
    {
        $dataKey = sprintf('%s%s%s', self::APCU_DATA_KEY, self::APCU_DELIMITER, $sessionId);
        $timeKey = sprintf('%s%s%s', self::APCU_TIME_KEY, self::APCU_DELIMITER, $sessionId);

        // return cached value if found in cache and is not too old
        if (
            count(apcu_exists([$dataKey, $timeKey])) === 2 &&
            (DateTimeUtils::getUtcDateTime())->getTimestamp() < (int) apcu_fetch($timeKey) + $this->timeout
        ) {
            return (string) apcu_fetch($dataKey);
        }

        $data = $this->handler->read($sessionId);
        $this->updateCache($sessionId, $data);

        return $data;
    }

    /**
     * @param string $sessionId
     * @param string $sessionData
     *
     * @return bool
     * @throws DateTimeException
     */
    public function write($sessionId, $sessionData): bool
    {
        $this->updateCache($sessionId, $sessionData);

        return $this->handler->write($sessionId, $sessionData);
    }

    /**
     * @param string $sessionId
     *
     * @return bool
     */
    public function destroy($sessionId): bool
    {
        apcu_delete(sprintf('%s%s%s', self::APCU_DATA_KEY, self::APCU_DELIMITER, $sessionId));
        apcu_delete(sprintf('%s%s%s', self::APCU_TIME_KEY, self::APCU_DELIMITER, $sessionId));

        return $this->handler->destroy($sessionId);
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        return $this->handler->close();
    }

    /**
     * @param int $maxLifetime
     *
     * @return bool
     */
    public function gc($maxLifetime): bool
    {
        return $this->handler->gc($maxLifetime);
    }

    /**
     * @param string $savePath
     * @param string $name
     *
     * @return bool
     */
    public function open($savePath, $name): bool
    {
        return $this->handler->open($savePath, $name);
    }

    /**
     * @param string $sessionId
     * @param string $sessionData
     *
     * @throws DateTimeException
     */
    private function updateCache(string $sessionId, string $sessionData): void
    {
        apcu_store(sprintf('%s%s%s', self::APCU_DATA_KEY, self::APCU_DELIMITER, $sessionId), $sessionData);
        apcu_store(
            sprintf('%s%s%s', self::APCU_TIME_KEY, self::APCU_DELIMITER, $sessionId),
            DateTimeUtils::getUtcDateTime()->getTimestamp()
        );
    }

}
