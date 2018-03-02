<?php declare(strict_types=1);

namespace Tests\Unit\Crypt;

use Exception;
use Hanaboso\CommonsBundle\Crypt\CryptException;
use Hanaboso\CommonsBundle\Crypt\CryptService;
use stdClass;
use Tests\KernelTestCaseAbstract;

/**
 * Class CryptServiceTest
 *
 * @package Tests\Unit\Crypt
 */
final class CryptServiceTest extends KernelTestCaseAbstract
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
            $encrypted = CryptService::encrypt($item);
            $decrypted = CryptService::decrypt($encrypted);
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

        $encrypted = CryptService::encrypt($str);

        $this->expectException(CryptException::class);
        $this->expectExceptionCode(CryptException::UNKNOWN_PREFIX);

        CryptService::decrypt('abc' . $encrypted);
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
        $encryptedStr = CryptService::encrypt($str);

        $arr          = ['key' => 'val', 'str' => $encryptedStr];
        $encryptedArr = CryptService::encrypt($arr);

        $decryptedArr = CryptService::decrypt($encryptedArr);
        $decryptedStr = CryptService::decrypt($decryptedArr['str']);

        $this->assertEquals($str, $decryptedStr);
        $this->assertEquals($arr, $decryptedArr);
    }

}