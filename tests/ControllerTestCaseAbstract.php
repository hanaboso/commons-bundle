<?php declare(strict_types=1);

namespace CommonsBundleTests;

use Hanaboso\PhpCheckUtils\PhpUnit\Traits\ControllerTestTrait;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\RestoreErrorHandlersTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ControllerTestCaseAbstract
 *
 * @package CommonsBundleTests
 */
abstract class ControllerTestCaseAbstract extends WebTestCase
{

    use ControllerTestTrait;
    use RestoreErrorHandlersTrait;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient([], []);
    }

    /**
     * @return void
     */
    protected function tearDown(): void {
        parent::tearDown();

        $this->restoreErrorHandler();
        $this->restoreExceptionHandler();
    }

}
