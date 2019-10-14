<?php declare(strict_types=1);

namespace CommonsBundleTests;

use Hanaboso\PhpCheckUtils\PhpUnit\Traits\ControllerTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ControllerTestCaseAbstract
 *
 * @package CommonsBundleTests
 */
abstract class ControllerTestCaseAbstract extends WebTestCase
{

    use ControllerTestTrait;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient([], []);
    }

}
