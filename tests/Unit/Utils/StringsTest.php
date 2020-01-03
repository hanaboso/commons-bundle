<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Utils;

use Hanaboso\CommonsBundle\Utils\Strings;
use PHPUnit\Framework\TestCase;

/**
 * Class StringsTest
 *
 * @package CommonsBundleTests\Unit\Utils
 */
final class StringsTest extends TestCase
{

    /**
     * @dataProvider toCamelCaseDataProvider
     *
     * @param string $string
     * @param string $assert
     * @param bool   $firstUpper
     */
    public function testToCamelCase(string $string, string $assert, bool $firstUpper): void
    {
        $camelCase = Strings::toCamelCase($string, $firstUpper);
        self::assertSame($assert, $camelCase);
    }

    /**
     * @return mixed[]
     */
    public function toCamelCaseDataProvider(): array
    {
        return [
            [
                'some_group',
                'SomeGroup',
                FALSE,
            ],
            [
                'some_group',
                'someGroup',
                TRUE,
            ],
            [
                'some_group_some_group',
                'someGroupSomeGroup',
                TRUE,
            ],
        ];
    }

    /**
     *
     */
    public function testGetShortClassName(): void
    {
        self::assertSame('StringsTest', Strings::getShortClassName($this));
    }

}
