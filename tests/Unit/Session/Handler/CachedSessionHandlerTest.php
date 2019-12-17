<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Session\Handler;

use Exception;
use Hanaboso\CommonsBundle\Session\Handler\CachedSessionHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SessionHandlerInterface;

/**
 * Class CachedSessionHandlerTest
 *
 * @package CommonsBundleTests\Unit\Session\Handler
 */
final class CachedSessionHandlerTest extends TestCase
{

    /**
     * @var CachedSessionHandler
     */
    private CachedSessionHandler $csh;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        /** @var SessionHandlerInterface | MockObject $sh */
        $sh = self::createMock(SessionHandlerInterface::class);
        $sh->expects(self::any())->method('destroy')->willReturn(TRUE);
        $sh->expects(self::any())->method('write')->willReturn(TRUE);
        $sh->expects(self::any())->method('read')->willReturn('default');

        $this->csh = new CachedSessionHandler($sh);
    }

    /**
     * @throws Exception
     */
    public function testApcu(): void
    {
        self::assertEquals([], apcu_exists(['foo', 'bar']));

        self::assertTrue(apcu_add('foo', 'val'));
        self::assertEquals(['foo' => TRUE], apcu_exists(['foo', 'bar']));
        self::assertEquals('val', apcu_fetch('foo'));
        self::assertFalse(apcu_fetch('bar'));

        self::assertTrue(apcu_add('bar', 'val'));
        self::assertEquals(['foo' => TRUE, 'bar' => TRUE], apcu_exists(['foo', 'bar']));
        self::assertEquals('val', apcu_fetch('foo'));
        self::assertEquals('val', apcu_fetch('bar'));

        self::assertTrue(apcu_delete('foo'));
        self::assertTrue(apcu_delete('bar'));
        self::assertEquals([], apcu_exists(['foo', 'bar']));
    }

    /**
     * @covers CachedSessionHandler::read()
     * @covers CachedSessionHandler::write()
     * @covers CachedSessionHandler::destroy()
     * @throws Exception
     */
    public function testReadWriteDestroy(): void
    {
        self::assertTrue($this->csh->destroy('foo'));
        self::assertEquals('default', $this->csh->read('foo'));

        self::assertTrue($this->csh->write('foo', 'bar'));
        self::assertEquals('bar', $this->csh->read('foo'));

        self::assertTrue($this->csh->destroy('foo'));
        self::assertEquals('default', $this->csh->read('foo'));
    }

    /**
     * @covers CachedSessionHandler::read()
     * @throws Exception
     */
    public function testCacheTimeout(): void
    {
        $this->csh->setTimeout(1);

        self::assertTrue($this->csh->destroy('foo'));
        self::assertEquals('default', $this->csh->read('foo'));

        self::assertTrue($this->csh->write('foo', 'val'));
        self::assertEquals('val', $this->csh->read('foo'));

        sleep(1);
        self::assertEquals('default', $this->csh->read('foo'));
    }

}
