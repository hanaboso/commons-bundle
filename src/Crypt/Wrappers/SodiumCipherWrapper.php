<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Crypt\Wrappers;

use Windwalker\Crypt\Cipher\SodiumCipher;

/**
 * Class SodiumCipherWrapper
 *
 * @package Hanaboso\CommonsBundle\Crypt\Wrappers
 */
final class SodiumCipherWrapper extends SodiumCipher
{

    private const PBKDF2_CACHE = 'pbkdf2_%s_%s';

    /**
     * @param string      $key
     * @param string|null $pbkdf2Salt
     */
    protected function derivateSecureKeys($key, $pbkdf2Salt = NULL): void
    {
        if (!$pbkdf2Salt) {
            if (!$this->pbkdf2Salt) {
                $this->pbkdf2Salt = $this->randomPseudoBytes(self::PBKDF2_SALT_BYTE_SIZE);
            }

            $pbkdf2Salt = $this->pbkdf2Salt;
        }

        if (!apcu_exists(sprintf(self::PBKDF2_CACHE, $key, $pbkdf2Salt))) {
            $iteration = $this->options['pbkdf2_iteration'] ?: 12_000;
            $pbkdf2    = $this->pbkdf2(
                self::PBKDF2_HASH_ALGORITHM,
                $key,
                $pbkdf2Salt,
                $iteration,
                self::PBKDF2_HASH_BYTE_SIZE,
                TRUE,
            );
            apcu_store(self::PBKDF2_CACHE, $pbkdf2, 3_600);
        }

        [$this->secureEncryptionKey, $this->secureHMACKey] = str_split(
            apcu_fetch(self::PBKDF2_CACHE),
            self::PBKDF2_HASH_BYTE_SIZE,
        );
    }

    /**
     * @param string $algorithm
     * @param string $password
     * @param string $salt
     * @param int    $count
     * @param int    $keyLength
     * @param bool   $rawOutput
     *
     * @return string
     */
    protected function pbkdf2($algorithm, $password, $salt, $count, $keyLength, $rawOutput = FALSE): string
    {
        return bin2hex(hash_pbkdf2(strtolower($algorithm), $password, $salt, $count, $keyLength, $rawOutput));
    }

}
