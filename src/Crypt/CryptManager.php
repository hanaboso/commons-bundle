<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Crypt;

use Hanaboso\CommonsBundle\Crypt\Exceptions\CryptException;

/**
 * Class CryptManager
 *
 * @package Hanaboso\CommonsBundle\Crypt
 */
final class CryptManager
{

    public const PREFIX_LENGTH = 4;

    /**
     * @var CryptInterface[]
     */
    private array $providers = [];

    /**
     * CryptManager constructor.
     *
     * @param mixed[] $cryptProviders
     */
    public function __construct(array $cryptProviders = [])
    {
        foreach ($cryptProviders as $provider) {
            if ($provider instanceof CryptInterface) {
                $this->providers[$provider->getPrefix()] = $provider;
            }
        }
    }

    /**
     * Encrypt data by concrete crypt service impl
     *
     * @param mixed       $data
     * @param string|null $prefix
     *
     * @return string
     * @throws CryptException
     */
    public function encrypt($data, ?string $prefix = NULL): string
    {
        return self::getImplementation($prefix)->encrypt($data);
    }

    /**
     * Tries to identify which crypt service to use for decryption by prefix and passes hash to it
     *
     * @param string $data
     *
     * @return mixed
     * @throws CryptException
     */
    public function decrypt(string $data)
    {
        $prefix = substr($data, 0, self::PREFIX_LENGTH);

        return $this->getImplementation($prefix)->decrypt($data);
    }

    /**
     * @param string $encryptedData
     * @param string $newCryptProviderPrefix
     *
     * @return string
     * @throws CryptException
     */
    public function transfer(string $encryptedData, string $newCryptProviderPrefix): string
    {
        return $this->encrypt($this->decrypt($encryptedData), $newCryptProviderPrefix);
    }

    /**
     * ---------------------------------------- HELPERS --------------------------------------
     */

    /**
     * @param string|null $prefix
     *
     * @return CryptInterface
     * @throws CryptException
     */
    private function getImplementation(?string $prefix): CryptInterface
    {
        // Pick first if provider not specified
        if ($prefix === NULL && !empty($this->providers)) {
            return reset($this->providers);
        }

        // Use selected provider
        if (array_key_exists($prefix ?? '', $this->providers)) {
            return $this->providers[$prefix];
        }

        // BC break
        if ($prefix === '00_') {
            throw new CryptException('The prefix was removed for license reasons.', CryptException::UNKNOWN_PREFIX);
        }

        throw new CryptException('Unknown crypt service prefix', CryptException::UNKNOWN_PREFIX);
    }

}
