<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Crypt;

use Hanaboso\CommonsBundle\Crypt\Exceptions\CryptException;

/**
 * Interface CryptInterface
 *
 * @package Hanaboso\CommonsBundle\Crypt
 */
interface CryptInterface
{

    /**
     * @param mixed $data
     *
     * @return string
     * @throws CryptException
     */
    public function encrypt($data): string;

    /**
     * @param string $data
     *
     * @return mixed
     * @throws CryptException
     */
    public function decrypt(string $data);

}