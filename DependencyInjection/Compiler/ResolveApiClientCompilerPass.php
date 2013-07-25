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
        $config = $container->getParameter('da_api_client.config');

        foreach ($config['api'] as $apiName => $apiConfig) {
            $service = $apiConfig['client']['service'];
            $implementor = $apiConfig['client']['implementor'];

            $implementorDef = new DefinitionDecorator($implementor);
            $implementorDef->isPublic(false);
            $implementorId = 'da_api_client.api_implementor.'.$apiName;
            $container->setDefinition($implementorId, $implementorDef);

            $serviceDef = new DefinitionDecorator($service);
            $serviceDef->isAbstract(false);
            $serviceDef->replaceArgument(0, new Reference($implementorId));
            $serviceDef->replaceArgument(1, $apiConfig);
            $container->setDefinition('da_api_client.api.'.$apiName, $serviceDef);
        }
    }
}
