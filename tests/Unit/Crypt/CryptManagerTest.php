<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Crypt;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Crypt\CryptManager;
use Hanaboso\CommonsBundle\Crypt\Exceptions\CryptException;
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

        foreach ($arr as $item) {
            $encrypted = CryptManager::encrypt($item);
            $decrypted = CryptManager::decrypt($encrypted);
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
        CryptManager::encrypt('hash', '00_');
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
        CryptManager::encrypt('hash', 'bad00_');
    }

}
