<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Session\Handler;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Session\Handler\RedisSessionHandler;

/**
 * Class RedisSessionHandlerTest
 *
 * @package CommonsBundleTests\Integration\Session\Handler
 */
final class RedisSessionHandlerTest extends KernelTestCaseAbstract
{

    /**
     * @var RedisSessionHandler
     */
    private RedisSessionHandler $handler;

    /**
     * Prepares handler for testing
     */
    public function setUp(): void
    {
        parent::setUp();
        $sessionHandler = self::$container->get('hbpf.commons.redis_session_handler');
        $this->handler  = $sessionHandler;
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Session\Handler\RedisSessionHandler::open()
     * @covers \Hanaboso\CommonsBundle\Session\Handler\RedisSessionHandler
     *
     * @throws Exception
     */
    public function testOpen(): void
    {
        self::assertTrue($this->handler->open('some/path', 'some name'));
        self::assertTrue($this->handler->open('some/path', 'another name'));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Session\Handler\RedisSessionHandler::close()
     *
     * @throws Exception
     */
    public function testClose(): void
    {
        self::assertTrue($this->handler->close());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Session\Handler\RedisSessionHandler::gc()
     *
     * @throws Exception
     */
    public function testGc(): void
    {
        self::assertTrue($this->handler->gc(0));
        self::assertTrue($this->handler->gc(999));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Session\Handler\RedisSessionHandler::read()
     * @covers \Hanaboso\CommonsBundle\Session\Handler\RedisSessionHandler::write()
     * @covers \Hanaboso\CommonsBundle\Session\Handler\RedisSessionHandler::destroy()
     *
     * @throws Exception
     */
    public function testReadWriteDestroy(): void
    {
        self::assertTrue($this->handler->destroy('foo'));
        self::assertEmpty($this->handler->read('foo'));
        self::assertTrue($this->handler->write('foo', 'data'));
        self::assertEquals('data', $this->handler->read('foo'));
        self::assertTrue($this->handler->write('foo', 'new data'));
        self::assertEquals('new data', $this->handler->read('foo'));
        self::assertTrue($this->handler->destroy('foo'));
        self::assertEmpty($this->handler->read('foo'));
    }

}
