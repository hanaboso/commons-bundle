<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Transport\Soap;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Transport\Soap\Dto\Wsdl\RequestDto;
use Hanaboso\CommonsBundle\Transport\Soap\SoapException;
use Hanaboso\CommonsBundle\Transport\Soap\SoapManager;

/**
 * Class SoapManagerTest
 *
 * @package CommonsBundleTests\Integration\Transport\Soap
 */
final class SoapManagerTest extends KernelTestCaseAbstract
{

    /**
     * @var SoapManager
     */
    private SoapManager $soap;

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

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @var SoapManager $soapManager */
        $soapManager = self::getContainer()->get('hbpf.transport.soap_manager');
        $this->soap  = $soapManager;
    }

}
