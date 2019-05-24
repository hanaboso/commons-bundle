<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Message;

/**
 * Interface MessageInterface
 *
 * @package Hanaboso\CommonsBundle\Message
 */
interface MessageInterface
{

    /**
     * @param string $data
     *
     * @return mixed
     */
    public function setData(string $data);

}
