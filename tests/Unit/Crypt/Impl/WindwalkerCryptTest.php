<?php declare(strict_types=1);

namespace Tests\Unit\Crypt\Impl;

use Exception;
use Hanaboso\CommonsBundle\Crypt\Exceptions\CryptException;
use Hanaboso\CommonsBundle\Crypt\Impl\WindwalkerCrypt;
use stdClass;
use Tests\KernelTestCaseAbstract;

/**
 * Class WindwalkerCryptTest
 *
 * @package Tests\Unit\Crypt\Impl
 */
final class WindwalkerCryptTest extends KernelTestCaseAbstract
{

    /**
     * @covers CryptService::encrypt()
     * @covers CryptService::decrypt()
     *
     * @throws Exception
     */
    public function testEncryptAndDecrypt(): void
    {
        $arr   = [];
        $arr[] = 'Some random text';
        $arr[] = 'docker://dkr.hanaboso.net/pipes/pipes/php-dev:dev/php /opt/project/pf-bundles/vendor/phpunit/phpunit/phpunit --configuration /opt/project/pf-bundles/phpunit.xml.dist Tests\Unit\Commons\Crypt\CryptServiceProviderTest /opt/project/pf-bundles/tests/Unit/Commons/Crypt/CryptServiceProviderTest.php --teamcity';
        $arr[] = ['1', '2', 3, ['abc']];

        $stdClass        = new stdClass();
        $stdClass->true  = TRUE;
        $stdClass->false = FALSE;
        $stdClass->arr   = ['foo'];
        $arr[]           = $stdClass;

        foreach ($arr as $item) {
            $encrypted = WindwalkerCrypt::encrypt($item);
            $decrypted = WindwalkerCrypt::decrypt($encrypted);

            $this->assertEquals($item, $decrypted);
        }
    }

    /**
     * @covers CryptService::encrypt()
     * @covers CryptService::decrypt()
     *
     * @throws Exception
     */
    public function testEncryptAndDecryptFail(): void
    {
        $str = 'Some random text';

        $encrypted = WindwalkerCrypt::encrypt($str);

        $this->expectException(CryptException::class);
        $this->expectExceptionCode(CryptException::UNKNOWN_PREFIX);

        WindwalkerCrypt::decrypt('abc' . $encrypted);
    }

    /**
     * @covers CryptService::encrypt()
     * @covers CryptService::decrypt()
     *
     * @throws Exception
     */
    public function testEncryptAndDecrypt2(): void
    {
        $str          = 'asdf12342~!@#$%^&*()_+{}|:"<>?[]\;,./';
        $encryptedStr = WindwalkerCrypt::encrypt($str);

        $arr          = ['key' => 'val', 'str' => $encryptedStr];
        $encryptedArr = WindwalkerCrypt::encrypt($arr);

        $decryptedArr = WindwalkerCrypt::decrypt($encryptedArr);
        $decryptedStr = WindwalkerCrypt::decrypt($decryptedArr['str']);

        $this->assertEquals($str, $decryptedStr);
        $this->assertEquals($arr, $decryptedArr);
    }

}