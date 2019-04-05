<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Crypt\Impl;

use Hanaboso\CommonsBundle\Crypt\CryptInterface;
use Hanaboso\CommonsBundle\Crypt\Exceptions\CryptException;
use Throwable;
use Windwalker\Crypt\Cipher\SodiumCipher;
use Windwalker\Crypt\Crypt;

/**
 * Class WindwalkerCrypt
 *
 * @package Hanaboso\CommonsBundle\Crypt\Impl
 */
final class WindwalkerCrypt implements CryptInterface
{

    public const  PREFIX = '01_';

    private const SECRET_KEY = '31E18744E366932F9EEEA104522DD30A77B0413A07F93D6B4E0C5F7F28E26A00E1B58AD1A665F129623170DC25F1C9F450F90BA8B74BF87258FF5507295755DCD356E220DF6B838C7596ECE49BDC89250987C86D82E672A1E4D2B0315386C6DA6C102AAW';

    /**
     * @param mixed $data
     *
     * @return string
     * @throws CryptException
     */
    public static function encrypt($data): string
    {
        try {
            $crypt = self::getCrypt();

            return sprintf('%s%s', self::PREFIX, $crypt->encrypt(serialize($data)));
        } catch (Throwable $t) {
            throw new CryptException($t->getMessage(), $t->getCode());
        }
    }

    /**
     * @param string $hash
     *
     * @return mixed
     * @throws CryptException
     */
    public static function decrypt(string $hash)
    {
        if (strpos($hash, self::PREFIX) !== 0) {
            throw new CryptException('Unknown prefix in hash.', CryptException::UNKNOWN_PREFIX);
        }

        try {
            $crypt = self::getCrypt();

            $hiddenString = $crypt->decrypt(substr($hash, strlen(self::PREFIX)));
        } catch (Throwable $t) {
            throw new CryptException($t->getMessage(), $t->getCode());
        }

        return unserialize($hiddenString);
    }

    /**
     * ------------------------------------------- HELPERS ----------------------------------------
     */

    /**
     * @return Crypt
     */
    private static function getCrypt(): Crypt
    {
        return new Crypt(new SodiumCipher(self::SECRET_KEY));
    }

}
