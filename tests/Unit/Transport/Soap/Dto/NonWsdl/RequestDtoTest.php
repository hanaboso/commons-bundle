<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Soap\Dto\NonWsdl;

use CommonsBundleTests\KernelTestCaseAbstract;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\NonWsdl\RequestDto;
use Hanaboso\CommonsBundle\Transport\Soap\SoapException;

/**
 * Class RequestDtoTest
 *
 * @package CommonsBundleTests\Unit\Transport\Soap\Dto\NonWsdl
 */
final class RequestDtoTest extends KernelTestCaseAbstract
{

    /**
     * @var RequestDto
     */
    private RequestDto $dto;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->dto = new RequestDto('fn', [], '', new Uri());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\NonWsdl\RequestDto::getType
     */
    public function testGetType(): void
    {
        self::assertEquals('non-wsdl', $this->dto->getType());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\RequestDtoAbstract::setAuth
     */
    public function testSetAuth(): void
    {
        $this->dto->setAuth('guest', 'guest');
        self::assertEmpty([]);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\RequestDtoAbstract::getFunction
     */
    public function testGetFunction(): void
    {
        self::assertEquals('fn', $this->dto->getFunction());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\RequestDtoAbstract::getArguments
     */
    public function testGetArguments(): void
    {
        self::assertEquals([], $this->dto->getArguments());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\RequestDtoAbstract::getHeader
     */
    public function testGetHeaders(): void
    {
        $this->dto->getHeader();
        self::assertEmpty([]);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Soap\Dto\RequestDtoAbstract::setVersion
     * @throws SoapException
     */
    public function testSetVersionErr(): void
    {
        self::expectException(SoapException::class);
        $this->dto->setVersion(5);
    }

}
