<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Event;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Event\ProcessStatusEvent;

/**
 * Class ProcessStatusEventTest
 *
 * @package CommonsBundleTests\Unit\Event
 */
final class ProcessStatusEventTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Event\ProcessStatusEvent::getProcessId
     * @covers \Hanaboso\CommonsBundle\Event\ProcessStatusEvent::getStatus
     * @covers \Hanaboso\CommonsBundle\Event\ProcessStatusEvent
     */
    public function testGetProcessId(): void
    {
        $processId = new ProcessStatusEvent('1', TRUE);
        self::assertEquals('1', $processId->getProcessId());
        self::assertTrue($processId->getStatus());
    }

}