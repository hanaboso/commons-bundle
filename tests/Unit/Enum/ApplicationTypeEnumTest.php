<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Enum;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Enum\ApplicationTypeEnum;

/**
 * Class ApplicationTypeEnumTest
 *
 * @package CommonsBundleTests\Unit\Enum
 */
final class ApplicationTypeEnumTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Enum\ApplicationTypeEnum::isWebhook
     */
    public function testIsWebhook(): void
    {
        self::assertTrue(ApplicationTypeEnum::isWebhook('webhook'));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Enum\ApplicationTypeEnum::isCron
     */
    public function testIsCron(): void
    {
        self::assertTrue(ApplicationTypeEnum::isCron('cron'));
    }

}
