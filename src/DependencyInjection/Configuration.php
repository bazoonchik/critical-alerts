<?php

namespace bazoonchik\CriticalAlertsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('critical_notifications');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('handlers')
                    ->children()
                        ->arrayNode('telegram')
                            ->children()
                                ->arrayNode('log_levels')
                                    ->scalarPrototype()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('sentry')
                            ->children()
                                ->arrayNode('log_levels')
                                    ->scalarPrototype()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}