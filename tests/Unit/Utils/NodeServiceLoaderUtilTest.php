<?php declare(strict_types=1);

namespace Tests\Unit\Utils;

use Hanaboso\CommonsBundle\Utils\NodeServiceLoaderUtil;
use Tests\KernelTestCaseAbstract;

/**
 * Class NodeServiceLoaderUtilTest
 *
 * @package Tests\Unit\Utils
 */
final class NodeServiceLoaderUtilTest extends KernelTestCaseAbstract
{

    /**
     *
     */
    public function testGetServices(): void
    {
        $path = self::$container->getParameter('kernel.root_dir');

        $dirs     = [
            sprintf('%s/testApp/config/', $path),
        ];
        $services = NodeServiceLoaderUtil::getServices($dirs, 'hbpf.connector');

        self::assertNotEmpty($services);
        self::assertTrue(in_array('null', $services));
        self::assertFalse(in_array('_defaults', $services));
        self::assertFalse(in_array('requestbin', $services));
    }

}