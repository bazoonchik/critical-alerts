<?php

namespace bazoonchik\CriticalAlertsBundle\DependencyInjection;

use bazoonchik\CriticalAlertsBundle\Handler\TelegramHandler;
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


        if (isset($config['handlers'])) {

            foreach ($config['handlers'] as $name => $handler) {
                switch ($handler['type']) {
                    case 'telegram':
                        $definition = $container->getDefinition(TelegramHandler::class)
                            ->setArgument('$apiKey', $handler['api_key'])
                            ->setArgument('$channel', $handler['channel'])
                            ->setArgument('$level', $handler['log_level']);

                        $container->setDefinition('monolog.handler' . $handler['type'], $definition);
                        break;
                }
            }
        }
    }
}