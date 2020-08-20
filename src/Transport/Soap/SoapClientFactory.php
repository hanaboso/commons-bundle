<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Soap;

use Hanaboso\CommonsBundle\Transport\Soap\Dto\RequestDtoAbstract;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SoapClient;
use Throwable;

/**
 * Class SoapClientFactory
 *
 * @package Hanaboso\CommonsBundle\Transport\Soap
 */
final class SoapClientFactory implements LoggerAwareInterface
{

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * SoapClientFactory constructor.
     */
    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return SoapClientFactory
     */
    public function setLogger(LoggerInterface $logger): SoapClientFactory
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param RequestDtoAbstract $request
     * @param mixed[]            $options
     *
     * @return SoapClient
     * @throws SoapException
     */
    public function create(RequestDtoAbstract $request, array $options): SoapClient
    {
        try {
            $wsdl = NULL;
            if ($request->getType() == SoapManagerInterface::MODE_WSDL) {
                $wsdl = strval($request->getUri());
            }

            return new SoapClient($wsdl, $options);
        } catch (Throwable $e) {
            $this->logger->error(sprintf('Invalid WSDL: %s', $e->getMessage()));

            throw new SoapException('Invalid WSDL.', SoapException::INVALID_WSDL, $e);
        }
    }

}
