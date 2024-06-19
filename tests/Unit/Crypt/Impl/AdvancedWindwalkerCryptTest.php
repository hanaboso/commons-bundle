<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Crypt\Impl;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Crypt\Exceptions\CryptException;
use Hanaboso\CommonsBundle\Crypt\Impl\AdwancedWindwalkerCrypt;
use Hanaboso\CommonsBundle\Crypt\Wrappers\SodiumCipherWrapper;
use PHPUnit\Framework\Attributes\CoversClass;
use stdClass;

/**
 * Class AdvancedWindwalkerCryptTest
 *
 * @package CommonsBundleTests\Unit\Crypt\Impl
 */
#[CoversClass(AdwancedWindwalkerCrypt::class)]
#[CoversClass(SodiumCipherWrapper::class)]
final class AdvancedWindwalkerCryptTest extends KernelTestCaseAbstract
{

    /**
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
        $crypt           = new AdwancedWindwalkerCrypt('ADFAF1A6A1SEASCA6FA6C1A26SEV6S6S26S2V6SVV+94S8363SDDV6SDV645');

        foreach ($arr as $item) {
            $encrypted = $crypt->encrypt($item);
            $decrypted = $crypt->decrypt($encrypted);

            self::assertEquals($item, $decrypted);
        }
    }

    /**
     * @throws Exception
     */
    public function testGetPrefix(): void
    {
        $crypt = new AdwancedWindwalkerCrypt('ADFAF1A6A1SEASCA6FA6C1A26SEV6S6S26S2V6SVV+94S8363SDDV6SDV645');
        self::assertEquals('002_', $crypt->getPrefix());

        self::expectException(CryptException::class);
        self::expectExceptionCode(CryptException::BAD_PREFIX_LENGTH);
        new AdwancedWindwalkerCrypt('ADFAF1A6A1SEASCA6FA6C1A26SEV6S6S26S2V6SVV+94S8363SDDV6SDV645', '01_');
    }

    /**
     * @throws Exception
     */
    public function testEncryptAndDecryptFail(): void
    {
        $str       = 'Some random text';
        $crypt     = new AdwancedWindwalkerCrypt('ADFAF1A6A1SEASCA6FA6C1A26SEV6S6S26S2V6SVV+94S8363SDDV6SDV645');
        $encrypted = $crypt->encrypt($str);

        self::expectException(CryptException::class);
        self::expectExceptionCode(CryptException::UNKNOWN_PREFIX);

        $crypt->decrypt(sprintf('abc%s', $encrypted));
    }

    /**
     * @throws Exception
     */
    public function testEncryptAndDecrypt2(): void
    {
        $crypt        = new AdwancedWindwalkerCrypt('ADFAF1A6A1SEASCA6FA6C1A26SEV6S6S26S2V6SVV+94S8363SDDV6SDV645');
        $str          = 'asdf12342~!@#$%^&*()_+{}|:"<>?[]\;,./';
        $encryptedStr = $crypt->encrypt($str);

        $arr          = ['key' => 'val', 'str' => $encryptedStr];
        $encryptedArr = $crypt->encrypt($arr);

        $decryptedArr = $crypt->decrypt($encryptedArr);
        $decryptedStr = $crypt->decrypt($decryptedArr['str']);

        self::assertEquals($str, $decryptedStr);
        self::assertEquals($arr, $decryptedArr);
    }

    /**
     * @throws CryptException
     */
    public function testEncryptErr(): void
    {
        $crypt = new AdwancedWindwalkerCrypt('ADFAF1A6A1SEASCA6FA6C1A26SEV6S6S26S2V6SVV+94S8363SDDV6SDV645');
        self::expectException(CryptException::class);
        $func = static function (): void {
            echo 'hello!';
        };
        $crypt->encrypt($func);
    }

    /**
     * @throws CryptException
     */
    public function testDecryptErr(): void
    {
        $crypt = new AdwancedWindwalkerCrypt('ADFAF');
        self::expectException(CryptException::class);
        self::expectExceptionCode(0);
        $crypt->decrypt('002_some_hash');
    }

}
