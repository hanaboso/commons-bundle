<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Hanaboso\CommonsBundle\DependencyInjection
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        return new TreeBuilder('hb_pf_common');
    }

}
