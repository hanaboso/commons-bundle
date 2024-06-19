<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Monolog;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Monolog\LogstashFormatterFactory;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class LogstashFormatterFactoryTest
 *
 * @package CommonsBundleTests\Unit\Monolog
 */
#[CoversClass(LogstashFormatterFactory::class)]
final class LogstashFormatterFactoryTest extends KernelTestCaseAbstract
{

    use CustomAssertTrait;

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $formatter = new LogstashFormatterFactory();
        $formatter->create('test');

        self::assertFake();
    }

}
