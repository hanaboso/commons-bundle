<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Crypt;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Crypt\CryptManager;
use stdClass;

/**
 * Class CryptManagerTest
 *
 * @package CommonsBundleTests\Unit\Crypt
 */
final class CryptManagerTest extends KernelTestCaseAbstract
{

    /**
     * @covers CryptManager::encrypt()
     * @covers CryptManager::decrypt()
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

}
