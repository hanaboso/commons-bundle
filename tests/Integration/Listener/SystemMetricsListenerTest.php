<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Listener;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Listener\SystemMetricsListener;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use Hanaboso\Utils\System\PipesHeaders;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

/**
 * Class SystemMetricsListenerTest
 *
 * @package CommonsBundleTests\Integration\Listener
 */
final class SystemMetricsListenerTest extends DatabaseTestCaseAbstract
{

    use PrivateTrait;
    use CustomAssertTrait;

    /**
     * @var SystemMetricsListener
     */
    private $listener;

    /**
     * @covers \Hanaboso\CommonsBundle\Listener\SystemMetricsListener::onKernelController
     */
    public function testOnKernelController(): void
    {
        $event = $this->createPartialMock(ControllerEvent::class, ['getRequest', 'isMainRequest']);
        $event->method('getRequest')->willThrowException(new Exception());
        $event->method('isMainRequest')->willReturn(TRUE);

        $this->listener->onKernelController($event);
        self::assertFake();
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Listener\SystemMetricsListener::onKernelTerminate
     */
    public function testOnKernelTerminate(): void
    {
        $request = new Request();
        $request->headers->set(PipesHeaders::TOPOLOGY_ID, '1');
        $request->headers->set(PipesHeaders::CORRELATION_ID, '1');
        $request->headers->set(PipesHeaders::NODE_ID, '1');

        $event = $this->createPartialMock(TerminateEvent::class, ['isMainRequest', 'getRequest']);
        $event->method('isMainRequest')->willReturn(TRUE);
        $event->method('getRequest')->willReturn($request);

        $this->listener->onKernelTerminate($event);
        self::assertFake();
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->listener = self::getContainer()->get('hbpf.system_metrics_listener');
    }

}
