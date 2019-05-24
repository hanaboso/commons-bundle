<?php declare(strict_types=1);

namespace Tests\Integration\Transport\Soap;

use Exception;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\Wsdl\RequestDto;
use Hanaboso\CommonsBundle\Transport\Soap\SoapException;
use Hanaboso\CommonsBundle\Transport\Soap\SoapManager;
use Tests\KernelTestCaseAbstract;

/**
 * Class SoapManagerTest
 *
 * @package Tests\Integration\Transport\Soap
 */
final class SoapManagerTest extends KernelTestCaseAbstract
{

    /**
     * @var SoapManager
     */
    private $soap;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @var SoapManager $soapManager */
        $soapManager = self::$container->get('hbpf.transport.soap_manager');
        $this->soap  = $soapManager;
    }

    /**
     * @throws Exception
     */
    public function testSendInvalidWsdl(): void
    {
        self::expectException(SoapException::class);
        self::expectExceptionCode(SoapException::INVALID_WSDL);

        $requestDto = (new RequestDto('function', [], 'namespcae', new Uri('http://google.cz')))->setVersion(1);
        self::assertEquals(200, $this->soap->send($requestDto)->getLastResponseHeaders());
    }

}
