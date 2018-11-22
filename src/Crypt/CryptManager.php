<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Crypt;

use Hanaboso\CommonsBundle\Crypt\Exceptions\CryptException;
use Hanaboso\CommonsBundle\Crypt\Impl\WindwalkerCrypt;

/**
 * Class CryptManager
 *
 * @package Hanaboso\CommonsBundle\Crypt
 */
class CryptManager
{

    public const PREFIX_LENGTH = 3;

    /**
     * Encrypt data by concrete crypt service impl
     *
     * @param mixed  $data
     * @param string $prefix
     *
     * @return string
     * @throws CryptException
     */
    public static function encrypt($data, string $prefix = WindwalkerCrypt::PREFIX): string
    {
        return self::getImplementation($prefix)::encrypt($data);
    }

    /**
     * Tries to identify which crypt service to use for decryption by prefix and passes hash to it
     *
     * @param string $data
     *
     * @return mixed
     * @throws CryptException
     */
    public static function decrypt(string $data)
    {
        $prefix = substr($data, 0, self::PREFIX_LENGTH);

        return self::getImplementation($prefix)::decrypt($data);
    }

    /**
     * ---------------------------------------- HELPERS --------------------------------------
     */

    /**
     * @param string $prefix
     *
     * @return CryptInterface
     * @throws CryptException
     */
    private static function getImplementation(string $prefix): CryptInterface
    {
        switch ($prefix) {
            // add new implementation of crypt services as you wish
            case WindwalkerCrypt::PREFIX:
                return new WindwalkerCrypt();
            case '00_':
                throw new CryptException('The prefix was removed for license reasons.', CryptException::UNKNOWN_PREFIX);
            default:
                throw new CryptException('Unknown crypt service prefix', CryptException::UNKNOWN_PREFIX);
        }
    }

}