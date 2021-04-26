<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Crypt\Impl;

use Hanaboso\CommonsBundle\Crypt\CryptImplAbstract;
use Hanaboso\CommonsBundle\Crypt\Exceptions\CryptException;
use Throwable;
use Windwalker\Crypt\Cipher\SodiumCipher;
use Windwalker\Crypt\Crypt;

/**
 * Class WindwalkerCrypt
 *
 * @package Hanaboso\CommonsBundle\Crypt\Impl
 */
final class WindwalkerCrypt extends CryptImplAbstract
{

    /**
     * WindwalkerCrypt constructor.
     *
     * @param string $secretKey
     * @param string $prefix
     *
     * @throws CryptException
     */
    public function __construct(private string $secretKey, string $prefix = '001_')
    {
        parent::__construct($prefix);
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

            return sprintf('%s%s', $this->getPrefix(), $crypt->encrypt(serialize($data)));
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
            $hiddenString = $this->getCrypt()->decrypt(substr($data, strlen($this->getPrefix())));

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
        return new Crypt(new SodiumCipher($this->secretKey));
    }

}
