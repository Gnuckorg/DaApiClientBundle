<?php

namespace Da\ApiClientBundle\DependencyInjection;

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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('da_api_client');

        $rootNode
            ->children()
                ->arrayNode('api')
                   ->useAttributeAsKey('dumb')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('base_url')
                                ->isRequired(true)
                            ->end()
                            ->scalarNode('api_token')
                                ->isRequired(true)
                            ->end()
                            ->scalarNode('cache_enabled')
                                ->defaultValue(true)
                            ->end()
                            ->arrayNode('client')
                                ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('service')
                                            ->defaultValue('da_api_client.api')
                                        ->end()
                                        ->scalarNode('implementor')
                                            ->defaultValue('da_api_client.api_implementor')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
