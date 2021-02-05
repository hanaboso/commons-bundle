<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Utils;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Utils\NodeServiceLoader;

/**
 * Class NodeServiceLoaderUtilTest
 *
 * @package CommonsBundleTests\Unit\Utils
 */
final class NodeServiceLoaderUtilTest extends KernelTestCaseAbstract
{

    /**
     *
     */
    public function testGetServices(): void
    {
        /** @var string $path */
        $path = self::$container->getParameter('kernel.project_dir');

        $dirs     = [
            sprintf('%s/tests/testApp/config/', $path),
        ];
        $services = NodeServiceLoader::getServices($dirs, 'hbpf.connector');

        self::assertNotEmpty($services);
        self::assertTrue(in_array('null', $services, TRUE));
        self::assertFalse(in_array('_defaults', $services, TRUE));
        self::assertFalse(in_array('requestbin', $services, TRUE));

        $services = NodeServiceLoader::getServices($dirs, 'hbpf.connector', ['null']);
        self::assertEquals(['null1', 'null2'], $services);
    }

}
