<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: Pavel Severyn
 * Date: 11.10.17
 * Time: 11:43
 */

namespace Hanaboso\CommonsBundle\Docker\Endpoint;

use Hanaboso\CommonsBundle\Docker\DockerClient;

/**
 * Class EndpointAbstract
 *
 * @package Hanaboso\CommonsBundle\Docker\Endpoint
 */
abstract class EndpointAbstract
{

    /**
     * @var DockerClient
     */
    protected $dockerClient;

    /**
     * Containers constructor.
     *
     * @param DockerClient $dockerClient
     */
    public function __construct(DockerClient $dockerClient)
    {
        $this->dockerClient = $dockerClient;
    }

}
