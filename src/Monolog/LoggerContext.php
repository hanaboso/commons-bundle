<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: radek.jirsa
 * Date: 12.11.18
 * Time: 8:55
 */

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
    private $exception;

    /**
     * @var string
     */
    private $correlation_id = '';

    /**
     * @var string
     */
    private $node_id = '';

    /**
     * @var string
     */
    private $node_name = '';

    /**
     * @var string
     */
    private $topology_id = '';

    /**
     * @var string
     */
    private $topology_name = '';

    /**
     * @param Throwable $exception
     *
     * @return LoggerContext
     */
    public function setException(Throwable $exception): LoggerContext
    {
        $this->exception = $exception;

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

        $this->correlation_id = PipesHeaders::get(PipesHeaders::CORRELATION_ID, $headers) ?? '';
        $this->node_id        = PipesHeaders::get(PipesHeaders::NODE_ID, $headers) ?? '';
        $this->node_name      = PipesHeaders::get(PipesHeaders::NODE_NAME, $headers) ?? '';
        $this->topology_id    = PipesHeaders::get(PipesHeaders::TOPOLOGY_ID, $headers) ?? '';
        $this->topology_name  = PipesHeaders::get(PipesHeaders::TOPOLOGY_NAME, $headers) ?? '';

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'exception'      => $this->exception,
            'correlation_id' => $this->correlation_id,
            'node_id'        => $this->node_id,
            'node_name'      => $this->node_name,
            'topology_id'    => $this->topology_id,
            'topology_name'  => $this->topology_name,
        ];
    }

}