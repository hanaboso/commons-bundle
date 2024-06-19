<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Exception;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Exception\OnRepeatException;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class OnRepeatExceptionTest
 *
 * @package CommonsBundleTests\Unit\Exception
 */
#[CoversClass(OnRepeatException::class)]
final class OnRepeatExceptionTest extends KernelTestCaseAbstract
{

    /**
     * @return void
     */
    public function testOnRepeatException(): void
    {
        $exception = new OnRepeatException(new ProcessDto());
        $exception->setMaxHops(4);
        $exception->setInterval(70_000);

        self::assertEquals(70_000, $exception->getInterval());
        self::assertEquals(4, $exception->getMaxHops());
        self::assertNotEmpty($exception->getProcessDto());
    }

}
