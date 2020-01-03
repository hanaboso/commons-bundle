<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Exception;

use Exception;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Throwable;

/**
 * Class OnRepeatException
 *
 * @package Hanaboso\CommonsBundle\Exception
 */
class OnRepeatException extends Exception
{

    /**
     * @var ProcessDto
     */
    private ProcessDto $processDto;

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
     * @param ProcessDto     $processDto
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(ProcessDto $processDto, $message = '', $code = 0, ?Throwable $previous = NULL)
    {
        parent::__construct($message, $code, $previous);

        $this->processDto = $processDto;
        $this->interval   = 60_000;
        $this->maxHops    = 3;
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
    public function setInterval(int $interval): OnRepeatException
    {
        $this->interval = $interval;

        return $this;
    }

    /**
     * @param int $maxHops
     *
     * @return OnRepeatException
     */
    public function setMaxHops(int $maxHops): OnRepeatException
    {
        $this->maxHops = $maxHops;

        return $this;
    }

    /**
     * @return ProcessDto
     */
    public function getProcessDto(): ProcessDto
    {
        return $this->processDto;
    }

}
