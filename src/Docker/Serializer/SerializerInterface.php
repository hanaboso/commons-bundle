<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: Pavel Severyn
 * Date: 10.10.17
 * Time: 16:05
 */

namespace Hanaboso\CommonsBundle\Docker\Serializer;

/**
 * Interface SerializerInterface
 *
 * @package Hanaboso\CommonsBundle\Docker\Serializer
 */
interface SerializerInterface
{

    /**
     * @param array $data
     *
     * @return string
     */
    public function serialize(array $data): string;

    /**
     * @param string $data
     *
     * @return array
     */
    public function deserialize(string $data): array;

}
