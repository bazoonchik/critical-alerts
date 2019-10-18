<?php

namespace bazoonchik\CriticalAlertsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Bundle\MonologBundle\DependencyInjection\Configuration as MonologConfiguration;

class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('critical_notifications');

        $monologConfiguration = (new MonologConfiguration())->getConfigTreeBuilder();
        $monologConfiguration->getRootNode()
            ->children()
                ->arrayNode('handlers')
                    ->prototype('array')
                        ->validate()
                            ->ifTrue(function ($v) {
                                return (
                                    'telegram' === $v['type'] &&
                                    (
                                        empty($v['api_key']) ||
                                        empty($v['channel']) ||
                                        empty($v['level'])
                                    )
                                );
                            })
                            ->thenInvalid('The api_key, channel, and log_level of telegram type must be configured.')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}