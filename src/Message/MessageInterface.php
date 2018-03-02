<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: marcel.pavlicek
 * Date: 3/6/17
 * Time: 5:27 PM
 */

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
