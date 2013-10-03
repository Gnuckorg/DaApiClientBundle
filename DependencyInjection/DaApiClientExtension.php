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
            $service = $apiConfiguration['client']['service'];
            $implementor = $apiConfiguration['client']['implementor'];

            $implementorDefinition = new DefinitionDecorator($implementor);
            $implementorDefinition->isPublic(false);
            $implementorId = sprintf('da_api_client.api_implementor.%s', $apiName);
            $container->setDefinition($implementorId, $implementorDefinition);
            $implementorDefinition->addTag('da_api_client.api_implementor');

            $serviceDefinition = new DefinitionDecorator($service);
            $serviceDefinition->isAbstract(false);
            $serviceDefinition->replaceArgument(0, new Reference($implementorId));
            $serviceDefinition->replaceArgument(1, $apiConfiguration);
            $serviceDefinition->addTag('da_api_client.api');
            $container->setDefinition(
                sprintf('da_api_client.api.%s', $apiName),
                $serviceDefinition
            );
        }
    }
}
