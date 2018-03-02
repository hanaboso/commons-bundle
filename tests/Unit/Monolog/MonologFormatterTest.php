<?php declare(strict_types=1);

namespace Tests\Unit\Monolog;

use Hanaboso\CommonsBundle\Exception\CronException;
use Hanaboso\CommonsBundle\Monolog\MonologFormatter;
use PHPUnit\Framework\TestCase;

/**
 * Class MonologFormatterTest
 *
 * @package Tests\Unit\Monolog
 */
class MonologFormatterTest extends TestCase
{

    /**
     *
     */
    public function testFormatException(): void
    {
        $exception = new CronException('Cron does not exists!', CronException::CRON_EXCEPTION);
        $expected  = sprintf(
            'Hanaboso\CommonsBundle\Exception\CronException %s: Cron does not exists!',
            CronException::CRON_EXCEPTION
        );
        $this->assertEquals($expected, MonologFormatter::formatException($exception));
    }

    /**
     *
     */
    public function testFormatString(): void
    {
        $this->assertEquals('String :)', MonologFormatter::formatString('String :)'));
    }

}