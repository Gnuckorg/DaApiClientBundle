<?php

namespace Da\ApiClientBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DaApiClientExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        foreach ($config['api'] as $apiName => $apiConfiguration) {
            $apiConfiguration = array_merge(
                $apiConfiguration,
                array('log_enabled' => $config['log_enabled'])
            );
            $service = $apiConfiguration['client']['service'];
            $implementor = $apiConfiguration['client']['implementor'];

            $implementorDefinition = new DefinitionDecorator($implementor);
            $implementorDefinition->isPublic(false);
            $implementorId = sprintf('da_api_client.api_implementor.%s', $apiName);
            $container->setDefinition($implementorId, $implementorDefinition);
            $implementorDefinition->addTag('da_api_client.api_implementor');

            $serviceDefinition = new DefinitionDecorator($service);
            $serviceDefinition->replaceArgument(0, new Reference($implementorId));
            $serviceDefinition->replaceArgument(1, $apiConfiguration);

            if (null !== $config['http_cacher']) {
                $serviceDefinition->addMethodCall('setCacher', array(
                    new Reference($config['http_cacher'])
                ));
            }

            $serviceDefinition->addMethodCall('setLogger', array(
                new Reference('da_api_client.http_logger')
            ));

            $serviceDefinition->addTag('da_api_client.api', array('name' => $apiName));
            $container->setDefinition(
                sprintf('da_api_client.api.%s', $apiName),
                $serviceDefinition
            );
        }
    }
}
