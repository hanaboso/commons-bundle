<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Soap\Dto;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\RequestHeaderDto;

/**
 * Class RequestHeaderDtoTest
 *
 * @package CommonsBundleTests\Unit\Transport\Soap\Dto
 */
final class RequestHeaderDtoTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\RequestHeaderDto::getNamespace
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\RequestHeaderDto::getParams
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\RequestHeaderDto::setParam
     */
    public function testHeaderDto(): void
    {
        $dto = new RequestHeaderDto('/namespace/', ['param1' => 1, 'param2' => 2]);

        self::assertEquals(
            [
                'param1' => 1,
                'param2' => 2,
            ],
            $dto->getParams(),
        );
        self::assertEquals('/namespace/', $dto->getNamespace());
        $dto->setParam('param1', 5);

        self::assertEquals(
            [
                'param1' => 5,
                'param2' => 2,
            ],
            $dto->getParams(),
        );
    }

}
