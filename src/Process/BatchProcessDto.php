<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Process;

use Hanaboso\Utils\String\Json;
use Hanaboso\Utils\System\PipesHeaders;

/**
 * Class BatchProcessDto
 *
 * @package Hanaboso\CommonsBundle\Process
 */
final class BatchProcessDto extends ProcessDtoAbstract
{

    public const BATCH_CURSOR_WITH_FOLLOWERS = 1_010;
    public const BATCH_CURSOR_ONLY           = 1_011;

    /**
     * @var BatchMessageDto[]
     */
    private array $messages;

    /**
     * BatchProcessDto constructor.
     *
     * @param mixed[] $commonHeaders
     */
    public function __construct(array $commonHeaders = [])
    {
        parent::__construct();

        $this->messages = [];
        $this->headers  = $commonHeaders;
    }

    /**
     * @param mixed[]     $body
     * @param string|NULL $user
     *
     * @return $this
     */
    public function addItem(array | string $body, ?string $user = NULL): self
    {
        /** @var string $b */
        $b = $body;
        if (gettype($body) !== 'string') {
            $b = Json::encode($body);
        }

        $this->messages[] = new BatchMessageDto($b, $user ? [PipesHeaders::USER => $user] : []);

        return $this;
    }

    /**
     * @param mixed[] $list
     *
     * @return $this
     */
    public function setItemList(array $list): self
    {
        foreach ($list as $value) {
            $this->addItem($value);
        }

        return $this;
    }

    /**
     * @param BatchMessageDto $message
     *
     * @return $this
     */
    public function addMessage(BatchMessageDto $message): self
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * @return BatchMessageDto[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param BatchMessageDto[] $messages
     *
     * @return $this
     */
    public function setMessages(array $messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * @param string $cursor
     * @param bool   $iterateOnly
     *
     * @return BatchProcessDto
     */
    public function setBatchCursor(string $cursor, bool $iterateOnly = FALSE): self
    {
        $this->addHeader(PipesHeaders::BATCH_CURSOR, $cursor);
        if ($iterateOnly) {
            $this->setStatusHeader(
                self::BATCH_CURSOR_ONLY,
                sprintf('Message will be used as a iterator with cursor [%s]. No follower will be called.', $cursor),
            );
        } else {
            $this->setStatusHeader(
                self::BATCH_CURSOR_WITH_FOLLOWERS,
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
    public function removeBatchCursor(): self
    {
        $this->removeHeader(PipesHeaders::BATCH_CURSOR);
        $this->removeRelatedHeaders([self::BATCH_CURSOR_ONLY, self::BATCH_CURSOR_WITH_FOLLOWERS]);

        return $this;
    }

    /**
     * @return string
     */
    public function getBridgeData(): string
    {
        return Json::encode($this->messages);
    }

}
