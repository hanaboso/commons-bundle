<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Soap\Dto\NonWsdl;

use CommonsBundleTests\KernelTestCaseAbstract;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\NonWsdl\RequestDto;
use Hanaboso\CommonsBundle\Transport\Soap\SoapException;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class RequestDtoTest
 *
 * @package CommonsBundleTests\Unit\Transport\Soap\Dto\NonWsdl
 */
#[CoversClass(RequestDto::class)]
final class RequestDtoTest extends KernelTestCaseAbstract
{

    use CustomAssertTrait;

    /**
     * @var RequestDto
     */
    private RequestDto $dto;

    /**
     * @return void
     */
    public function testGetType(): void
    {
        self::assertEquals('non-wsdl', $this->dto->getType());
    }

    /**
     * @return void
     */
    public function testSetAuth(): void
    {
        $this->dto->setAuth('guest', 'guest');
        self::assertFake();
    }

    /**
     * @return void
     */
    public function testGetFunction(): void
    {
        self::assertEquals('fn', $this->dto->getFunction());
    }

    /**
     * @return void
     */
    public function testGetArguments(): void
    {
        self::assertEquals([], $this->dto->getArguments());
    }

    /**
     * @throws SoapException
     */
    public function testSetVersionErr(): void
    {
        self::expectException(SoapException::class);
        $this->dto->setVersion(5);
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->dto = new RequestDto('fn', [], '', new Uri());
    }

}
