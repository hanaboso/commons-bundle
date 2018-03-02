<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport;

use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\ResponseDto;

/**
 * Interface CurlManagerInterface
 *
 * @package Hanaboso\CommonsBundle\Transport
 */
interface CurlManagerInterface
{

    /**
     * @param RequestDto $dto
     * @param array      $options
     *
     * @return ResponseDto
     */
    public function send(RequestDto $dto, array $options = []): ResponseDto;

}