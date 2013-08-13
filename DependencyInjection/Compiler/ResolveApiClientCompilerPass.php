<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * ResolveApiClientCompilerPass resolve the API client from the configuration.
 *
 * @author Thomas Prelot
 */
class ResolveApiClientCompilerPass implements CompilerPassInterface
{
    /**
     * Process the ContainerBuilder to inject the configuration and the implementor
     * into the API client.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $configuration = $container->getParameter('da_api_client.configuration');

        foreach ($configuration['api'] as $apiName => $apiConfiguration) {
            $service = $apiConfiguration['client']['service'];
            $implementor = $apiConfiguration['client']['implementor'];

            $implementorDefinition = new DefinitionDecorator($implementor);
            $implementorDefinition->isPublic(false);
            $implementorId = sprintf('da_api_client.api_implementor.%s', $apiName);
            $container->setDefinition($implementorId, $implementorDefinition);

            $serviceDefinition = new DefinitionDecorator($service);
            $serviceDefinition->isAbstract(false);
            $serviceDefinition->replaceArgument(0, new Reference($implementorId));
            $serviceDefinition->replaceArgument(1, $apiConfiguration);
            $container->setDefinition(
                sprintf('da_api_client.api.%s', $apiName),
                $serviceDefinition
            );
        }
    }
}
