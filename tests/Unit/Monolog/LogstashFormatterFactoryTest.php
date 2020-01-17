<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Monolog;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Monolog\LogstashFormatterFactory;

/**
 * Class LogstashFormatterFactoryTest
 *
 * @package CommonsBundleTests\Unit\Monolog
 */
final class LogstashFormatterFactoryTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Monolog\LogstashFormatterFactory::create
     */
    public function testCreate(): void
    {
        $formatter = new LogstashFormatterFactory();
        $formatter->create('test');

        self::assertEmpty([]);
    }

}
