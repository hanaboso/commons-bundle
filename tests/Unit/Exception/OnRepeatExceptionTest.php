<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Exception;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Exception\OnRepeatException;
use Hanaboso\CommonsBundle\Process\ProcessDto;

/**
 * Class OnRepeatExceptionTest
 *
 * @package CommonsBundleTests\Unit\Exception
 */
final class OnRepeatExceptionTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Exception\OnRepeatException
     * @covers \Hanaboso\CommonsBundle\Exception\OnRepeatException::getInterval
     * @covers \Hanaboso\CommonsBundle\Exception\OnRepeatException::setInterval
     * @covers \Hanaboso\CommonsBundle\Exception\OnRepeatException::setMaxHops
     * @covers \Hanaboso\CommonsBundle\Exception\OnRepeatException::getProcessDto
     */
    public function testOnRepeatException(): void
    {
        $exception = new OnRepeatException(new ProcessDto());
        $exception->setMaxHops(4);
        $exception->setInterval(70_000);

        self::assertEquals(70_000, $exception->getInterval());
        self::assertEquals(4, $exception->getMaxHops());
        self::assertInstanceOf(ProcessDto::class, $exception->getProcessDto());
    }

}