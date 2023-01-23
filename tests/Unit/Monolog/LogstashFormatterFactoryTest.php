<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Monolog;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Monolog\LogstashFormatterFactory;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;

/**
 * Class LogstashFormatterFactoryTest
 *
 * @package CommonsBundleTests\Unit\Monolog
 */
final class LogstashFormatterFactoryTest extends KernelTestCaseAbstract
{

    use CustomAssertTrait;

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LogstashFormatterFactory::create
     */
    public function testCreate(): void
    {
        $formatter = new LogstashFormatterFactory();
        $formatter->create('test');

        self::assertFake();
    }

}
