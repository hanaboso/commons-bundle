<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Monolog;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\CommonsBundle\Utils\PipesHeaders;
use Throwable;

/**
 * Class LoggerContext
 *
 * @package Hanaboso\CommonsBundle\Monolog
 */
final class LoggerContext
{

    /**
     * @var Throwable
     */
    private Throwable $exception;

    /**
     * @var string
     */
    private string $correlationId;

    /**
     * @var string
     */
    private string $nodeId;

    /**
     * @var string
     */
    private string $nodeName;

    /**
     * @var string
     */
    private string $topologyId;

    /**
     * @var string
     */
    private string $topologyName;

    /**
     * @param Throwable $exception
     *
     * @return LoggerContext
     */
    public function setException(Throwable $exception): LoggerContext
    {
        $this->exception     = $exception;
        $this->correlationId = '';
        $this->nodeId        = '';
        $this->nodeName      = '';
        $this->topologyId    = '';
        $this->topologyName  = '';

        return $this;
    }

    /**
     * @param ProcessDto $dto
     *
     * @return LoggerContext
     */
    public function setHeaders(ProcessDto $dto): LoggerContext
    {
        $headers = $dto->getHeaders();

        $this->correlationId = PipesHeaders::get(PipesHeaders::CORRELATION_ID, $headers) ?? '';
        $this->nodeId        = PipesHeaders::get(PipesHeaders::NODE_ID, $headers) ?? '';
        $this->nodeName      = PipesHeaders::get(PipesHeaders::NODE_NAME, $headers) ?? '';
        $this->topologyId    = PipesHeaders::get(PipesHeaders::TOPOLOGY_ID, $headers) ?? '';
        $this->topologyName  = PipesHeaders::get(PipesHeaders::TOPOLOGY_NAME, $headers) ?? '';

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'exception'      => $this->exception,
            'correlation_id' => $this->correlationId,
            'node_id'        => $this->nodeId,
            'node_name'      => $this->nodeName,
            'topology_id'    => $this->topologyId,
            'topology_name'  => $this->topologyName,
        ];
    }

}
