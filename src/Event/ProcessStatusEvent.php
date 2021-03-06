<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class ProcessStatusEvent
 *
 * @package Hanaboso\CommonsBundle\Event
 */
final class ProcessStatusEvent extends Event
{

    public const PROCESS_FINISHED = 'finished';

    /**
     * ProcessStatusEvent constructor.
     *
     * @param string $processId
     * @param bool   $status
     */
    public function __construct(private string $processId, private bool $status)
    {
    }

    /**
     * @return string
     */
    public function getProcessId(): string
    {
        return $this->processId;
    }

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

}
