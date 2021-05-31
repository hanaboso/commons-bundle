<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Crypt;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Crypt\CryptInterface;
use Hanaboso\CommonsBundle\Crypt\CryptManager;
use Hanaboso\CommonsBundle\Crypt\Exceptions\CryptException;
use Hanaboso\CommonsBundle\Crypt\Impl\WindwalkerCrypt;
use stdClass;

/**
 * Class CryptManagerTest
 *
 * @package CommonsBundleTests\Unit\Crypt
 */
final class CryptManagerTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Crypt\CryptManager::encrypt()
     * @covers \Hanaboso\CommonsBundle\Crypt\CryptManager::decrypt()
     * @covers \Hanaboso\CommonsBundle\Crypt\CryptManager::getImplementation()
     * @covers \Hanaboso\CommonsBundle\Crypt\CryptManager::__construct()
     *
     * @throws Exception
     */
    public function testEncryptAndDecrypt(): void
    {
        $arr   = [];
        $arr[] = 'Some random text';
        $arr[] = 'docker://dkr.hanaboso.net/pipes/pipes/php-dev:dev/php /opt/project/pf-bundles/vendor/phpunit/phpunit/phpunit --configuration /opt/project/pf-bundles/phpunit.xml.dist CommonsBundleTests\Unit\Commons\Crypt\CryptServiceProviderTest /opt/project/pf-bundles/tests/Unit/Commons/Crypt/CryptServiceProviderTest.php --teamcity';
        $arr[] = ['1', '2', 3, ['abc']];

        $stdClass        = new stdClass();
        $stdClass->true  = TRUE;
        $stdClass->false = FALSE;
        $stdClass->arr   = ['foo'];
        $arr[]           = $stdClass;
        $cryptManager    = $this->getCryptManager();

        foreach ($arr as $item) {
            $encrypted = $cryptManager->encrypt($item);
            $decrypted = $cryptManager->decrypt($encrypted);
            self::assertEquals($item, $decrypted);
        }
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Crypt\CryptManager::decrypt()
     * @covers \Hanaboso\CommonsBundle\Crypt\CryptManager::getImplementation()
     *
     * @throws Exception
     */
    public function testDecryptUnsupportedImpl(): void
    {
        self::expectException(CryptException::class);
        self::expectExceptionCode(CryptException::UNKNOWN_PREFIX);
        $cryptManager = $this->getCryptManager();
        $cryptManager->encrypt('hash', '00_');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Crypt\CryptManager::decrypt()
     * @covers \Hanaboso\CommonsBundle\Crypt\CryptManager::getImplementation()
     *
     * @throws Exception
     */
    public function testDecryptBadImpl(): void
    {
        self::expectException(CryptException::class);
        self::expectExceptionCode(CryptException::UNKNOWN_PREFIX);
        $cryptManager = $this->getCryptManager();
        $cryptManager->encrypt('hash', 'bad00_');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Crypt\CryptManager::transfer()
     *
     * @throws Exception
     */
    public function testTransfer(): void
    {
        $expected     = ['string' => 'awdawd24ad5a2d5a2d55ad5'];
        $cryptManager = $this->getCryptManager([new WindwalkerCrypt('11111111111+AFAFASFSEF8443513513854AWD', '010_')]);

        $encryptedData  = $cryptManager->encrypt($expected);
        $newCryptedData = $cryptManager->transfer($encryptedData, '010_');
        $transferedData = $cryptManager->decrypt($newCryptedData);
        self::assertEquals($expected, $transferedData);
    }

    /**
     * -------------------------------------------- HELPERS ----------------------------------------
     */

    /**
     * @param CryptInterface[] $anotherProviders
     *
     * @return CryptManager
     */
    private function getCryptManager(array $anotherProviders = []): CryptManager
    {
        return new CryptManager(
            [new WindwalkerCrypt('ADFAF1A6A1SEASCA6FA6C1A26SEV6S6S26S2V6SVV+94S8363SDDV6SDV645'), ...$anotherProviders],
        );
    }

}
