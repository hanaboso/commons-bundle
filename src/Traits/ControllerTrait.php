<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Traits;

use Hanaboso\CommonsBundle\Utils\ControllerUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Trait ControllerTrait
 *
 * @package Hanaboso\CommonsBundle\Traits
 */
trait ControllerTrait
{

    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param mixed $data
     * @param int   $code
     * @param array $headers
     *
     * @return Response
     */
    protected function getResponse($data, int $code = 200, array $headers = []): Response
    {
        if (!is_string($data)) {
            $data = json_encode($data, JSON_THROW_ON_ERROR);
        } else if (!json_decode($data)) {
            $data = json_encode($data, JSON_THROW_ON_ERROR);
        }

        return new Response($data, $code, $headers);
    }

    /**
     * @param Throwable $e
     * @param int       $code
     * @param string    $status
     * @param array     $headers
     *
     * @return Response
     */
    protected function getErrorResponse(
        Throwable $e,
        int $code = 500,
        string $status = ControllerUtils::INTERNAL_SERVER_ERROR,
        array $headers = []
    ): Response
    {
        $msg     = ControllerUtils::createExceptionData($e, $status);
        $headers = ControllerUtils::createHeaders($headers, $e);

        if ($this->logger) {
            $this->logger->error($msg, ['exception' => $e]);
        }

        return $this->getResponse($msg, $code, $headers);
    }

}
