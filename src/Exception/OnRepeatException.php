<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Exception;

use Exception;
use Hanaboso\CommonsBundle\Process\ProcessDtoAbstract;
use Throwable;

/**
 * Class OnRepeatException
 *
 * @package Hanaboso\CommonsBundle\Exception
 */
final class OnRepeatException extends Exception
{

    /**
     * interval in ms
     *
     * @var int
     */
    private int $interval;

    /**
     * @var int
     */
    private int $maxHops;

    /**
     * OnRepeatException constructor.
     *
     * @param ProcessDtoAbstract $processDto
     * @param string             $message
     * @param int                $code
     * @param Throwable|null     $previous
     */
    public function __construct(
        private ProcessDtoAbstract $processDto,
        $message = '',
        $code = 0,
        ?Throwable $previous = NULL,
    )
    {
        parent::__construct($message, $code, $previous);

        $this->interval = 60_000;
        $this->maxHops  = 3;
    }

    /**
     * @return int
     */
    public function getInterval(): int
    {
        return $this->interval;
    }

    /**
     * @return int
     */
    public function getMaxHops(): int
    {
        return $this->maxHops;
    }

    /**
     * @param int $interval
     *
     * @return OnRepeatException
     */
    public function setInterval(int $interval): self
    {
        $this->interval = $interval;

        return $this;
    }

    /**
     * @param int $maxHops
     *
     * @return OnRepeatException
     */
    public function setMaxHops(int $maxHops): self
    {
        $this->maxHops = $maxHops;

        return $this;
    }

    /**
     * @return ProcessDtoAbstract
     */
    public function getProcessDto(): ProcessDtoAbstract
    {
        return $this->processDto;
    }

}
