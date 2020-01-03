<?php declare(strict_types=1);

namespace CommonsBundleTests;

use Aws\Symfony\AwsBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle;
use Exception;
use Hanaboso\CommonsBundle\HbPFCommonsBundle;
use Snc\RedisBundle\SncRedisBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * Class Kernel
 *
 * @package CommonsBundleTests
 */
final class Kernel extends BaseKernel
{

    use MicroKernelTrait;

    public const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        $contents = [
            AwsBundle::class              => ['all' => TRUE],
            FrameworkBundle::class        => ['all' => TRUE],
            DoctrineBundle::class         => ['all' => TRUE],
            DoctrineFixturesBundle::class => ['all' => TRUE],
            MonologBundle::class          => ['all' => TRUE],
            DoctrineMongoDBBundle::class  => ['all' => TRUE],
            SncRedisBundle::class         => ['all' => TRUE],
            HbPFCommonsBundle::class      => ['all' => TRUE],

        ];
        foreach ($contents as $class => $envs) {
            $envs;

            yield new $class();
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     *
     * @throws Exception
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->setParameter('container.dumper.inline_class_loader', TRUE);
        $loader->load(sprintf('%s/*%s', $this->getConfigDir(), self::CONFIG_EXTS), 'glob');
    }

    /**
     * @param RouteCollectionBuilder $routes
     *
     * @throws LoaderLoadException
     */
    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $routes->import(sprintf('%s/*%s', $this->getRoutingDir(), self::CONFIG_EXTS), '/', 'glob');
    }

    /**
     * @return string
     */
    private function getConfigDir(): string
    {
        return sprintf('%s/tests/testApp/config', $this->getProjectDir());
    }

    /**
     * @return string
     */
    private function getRoutingDir(): string
    {
        return sprintf('%s/tests/testApp/routing', $this->getProjectDir());
    }

}
