<?php

namespace Itkg\ReferenceApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('reference');

        $rootNode->children()
            ->arrayNode('facades')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('reference_type')->defaultValue('Itkg\ReferenceApiBundle\Facade\ReferenceTypeFacade')->end()
                    ->scalarNode('reference_type_collection')->defaultValue('Itkg\ReferenceApiBundle\Facade\ReferenceTypeCollectionFacade')->end()
                    ->scalarNode('reference')->defaultValue('Itkg\ReferenceApiBundle\Facade\ReferenceFacade')->end()
                    ->scalarNode('reference_collection')->defaultValue('Itkg\ReferenceApiBundle\Facade\ReferenceCollectionFacade')->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
