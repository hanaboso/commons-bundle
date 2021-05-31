<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Monolog;

use Hanaboso\CommonsBundle\Exception\CronException;
use Hanaboso\CommonsBundle\Monolog\MonologFormatter;
use PHPUnit\Framework\TestCase;

/**
 * Class MonologFormatterTest
 *
 * @package CommonsBundleTests\Unit\Monolog
 */
final class MonologFormatterTest extends TestCase
{

    /**
     *
     */
    public function testFormatException(): void
    {
        $exception = new CronException('Cron does not exists!', CronException::CRON_EXCEPTION);
        $expected  = sprintf(
            'Hanaboso\CommonsBundle\Exception\CronException %s: Cron does not exists!',
            CronException::CRON_EXCEPTION,
        );
        self::assertEquals($expected, MonologFormatter::formatException($exception));
    }

    /**
     *
     */
    public function testFormatString(): void
    {
        self::assertEquals('String :)', MonologFormatter::formatString('String :)'));
    }

}
