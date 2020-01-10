<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Session\Handler;

use Predis\Client;
use Predis\Connection\Parameters;
use SessionHandlerInterface;

/**
 * Class RedisSessionHandler
 *
 * @package Hanaboso\CommonsBundle\Session\Handler
 */
final class RedisSessionHandler implements SessionHandlerInterface
{

    /**
     * @var Client<mixed>
     */
    private Client $client;

    /**
     * @var int
     */
    private int $lifeTime;

    /**
     * RedisSessionHandler constructor.
     *
     * @param string $redisDsn
     * @param int    $lifeTime
     */
    public function __construct(string $redisDsn, $lifeTime = 86_400)
    {
        $this->client   = new Client(Parameters::create($redisDsn));
        $this->lifeTime = $lifeTime;
    }

    /**
     * @param string $savePath
     * @param string $name
     *
     * @return bool
     */
    public function open($savePath, $name): bool
    {
        $savePath;
        $name;

        return TRUE;
    }

    /**
     * @param string $sessionId
     *
     * @return string
     */
    public function read($sessionId): string
    {
        return $this->client->get($sessionId) ?? '';
    }

    /**
     * @param string $sessionId
     * @param string $sessionData
     *
     * @return bool
     */
    public function write($sessionId, $sessionData): bool
    {
        $this->client->setex($sessionId, $this->lifeTime, $sessionData);

        return TRUE;
    }

    /**
     * @param string $sessionId
     *
     * @return bool
     */
    public function destroy($sessionId): bool
    {
        $this->client->del([$sessionId]);

        return TRUE;
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        return TRUE;
    }

    /**
     * @param int $maxLifeTime
     *
     * @return bool
     */
    public function gc($maxLifeTime): bool
    {
        $maxLifeTime;

        return TRUE;
    }

}
