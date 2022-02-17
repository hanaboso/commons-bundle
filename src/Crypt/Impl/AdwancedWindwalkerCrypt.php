<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Crypt\Impl;

use Hanaboso\CommonsBundle\Crypt\CryptImplAbstract;
use Hanaboso\CommonsBundle\Crypt\Exceptions\CryptException;
use Hanaboso\CommonsBundle\Crypt\Wrappers\SodiumCipherWrapper;
use Throwable;
use Windwalker\Crypt\Crypt;

/**
 * Class AdwancedWindwalkerCrypt
 *
 * @package Hanaboso\CommonsBundle\Crypt\Impl
 */
final class AdwancedWindwalkerCrypt extends CryptImplAbstract
{

    /**
     * AdwancedWindwalkerCrypt constructor.
     *
     * @param string $secretKey
     * @param string $prefix
     *
     * @throws CryptException
     */
    public function __construct(private string $secretKey, string $prefix = '002_')
    {
        parent::__construct($prefix);

        $this->secretKey = self::normalizeLengthOfSecretKey($secretKey);
    }

    /**
     * @param mixed $data
     *
     * @return string
     * @throws CryptException
     */
    public function encrypt(mixed $data): string
    {
        try {
            $crypt = $this->getCrypt();

            return sprintf('%s%s', $this->getPrefix(), $crypt->encrypt(serialize($data), $this->secretKey));
        } catch (Throwable $t) {
            throw new CryptException($t->getMessage(), $t->getCode());
        }
    }

    /**
     * @param string $data
     *
     * @return mixed
     * @throws CryptException
     */
    public function decrypt(string $data): mixed
    {
        if (!str_starts_with($data, $this->getPrefix())) {
            throw new CryptException('Unknown prefix in hash.', CryptException::UNKNOWN_PREFIX);
        }

        try {
            $hiddenString = $this->getCrypt()->decrypt(substr($data, strlen($this->getPrefix())), $this->secretKey);

            return unserialize($hiddenString);
        } catch (Throwable $t) {
            throw new CryptException($t->getMessage(), $t->getCode());
        }
    }

    /**
     * ------------------------------------------- HELPERS ----------------------------------------
     */

    /**
     * @return Crypt
     */
    private function getCrypt(): Crypt
    {
        return new Crypt(new SodiumCipherWrapper($this->secretKey));
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private static function normalizeLengthOfSecretKey(string $key): string
    {
        $key = str_repeat($key, intval(ceil(SODIUM_CRYPTO_SECRETBOX_KEYBYTES / strlen($key))));

        if (strlen($key) > SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            return substr($key, 0, SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        }

        return $key;
    }

}
