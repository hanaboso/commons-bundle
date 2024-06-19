<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Event;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Event\ProcessStatusEvent;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class ProcessStatusEventTest
 *
 * @package CommonsBundleTests\Unit\Event
 */
#[CoversClass(ProcessStatusEvent::class)]
final class ProcessStatusEventTest extends KernelTestCaseAbstract
{

    /**
     * @return void
     */
    public function testGetProcessId(): void
    {
        $processId = new ProcessStatusEvent('1', TRUE);
        self::assertEquals('1', $processId->getProcessId());
        self::assertTrue($processId->getStatus());
    }

}
