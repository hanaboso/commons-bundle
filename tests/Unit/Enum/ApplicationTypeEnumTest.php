<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Enum;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Enum\ApplicationTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class ApplicationTypeEnumTest
 *
 * @package CommonsBundleTests\Unit\Enum
 */
#[CoversClass(ApplicationTypeEnum::class)]
final class ApplicationTypeEnumTest extends KernelTestCaseAbstract
{

    /**
     * @return void
     */
    public function testIsWebhook(): void
    {
        self::assertTrue(ApplicationTypeEnum::isWebhook('webhook'));
    }

    /**
     * @return void
     */
    public function testIsCron(): void
    {
        self::assertTrue(ApplicationTypeEnum::isCron('cron'));
    }

}
