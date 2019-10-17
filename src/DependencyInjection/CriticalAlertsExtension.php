<?php

namespace bazoonchik\CriticalAlertsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class CriticalAlertsExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resource'));
        $loader->load('services.yaml');

        $gorushClientDefaultOptions = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'base_uri' => $config['gorush_url'],
            'verify_peer' => $config['verify_peer'],
            'verify_host' => $config['verify_host']
        ];

        $container->getDefinition(PushService::class)
            ->setArgument('$gorushClientDefaultOptions', $gorushClientDefaultOptions);
    }
}