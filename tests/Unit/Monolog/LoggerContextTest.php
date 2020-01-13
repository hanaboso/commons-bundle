<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Monolog;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Monolog\LoggerContext;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\Utils\System\PipesHeaders;

/**
 * Class LoggerContextTest
 *
 * @package CommonsBundleTests\Unit\Monolog
 */
final class LoggerContextTest extends KernelTestCaseAbstract
{

    /**
     * @var LoggerContext
     */
    private LoggerContext $logger;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->logger = new LoggerContext();
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LoggerContext::setException
     */
    public function testException(): void
    {
        self::assertInstanceOf(LoggerContext::class, $this->logger->setException(new Exception()));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LoggerContext::setHeaders()
     * @covers \Hanaboso\CommonsBundle\Monolog\LoggerContext::toArray()
     */
    public function testSetHeaders(): void
    {
        $this->logger
            ->setHeaders(
                (new ProcessDto())
                    ->setHeaders(
                        [
                            PipesHeaders::CORRELATION_ID => '1',
                            PipesHeaders::NODE_ID        => '2',
                            PipesHeaders::NODE_NAME      => 'name',
                            PipesHeaders::TOPOLOGY_ID    => '1',
                            PipesHeaders::TOPOLOGY_NAME  => 'name',
                        ]
                    )
            )
            ->setException(new Exception());
        self::assertEquals(6, count($this->logger->toArray()));
    }

}