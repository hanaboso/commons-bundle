<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Crypt;

use Hanaboso\CommonsBundle\Crypt\Exceptions\CryptException;

/**
 * Class CryptImplAbstract
 *
 * @package Hanaboso\CommonsBundle\Crypt
 */
abstract class CryptImplAbstract implements CryptInterface
{

    /**
     * CryptImplAbstract constructor.
     *
     * @param string $prefix
     *
     * @throws CryptException
     */
    public function __construct(protected string $prefix)
    {
        if (strlen($prefix) !== CryptManager::PREFIX_LENGTH) {
            throw new CryptException(
                sprintf(
                    'Crypt prefix of class "%s" has bad length "%s", allowed length is %s.',
                    self::class,
                    strlen($prefix),
                    CryptManager::PREFIX_LENGTH
                ),
                CryptException::BAD_PREFIX_LENGTH
            );
        }
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

}
