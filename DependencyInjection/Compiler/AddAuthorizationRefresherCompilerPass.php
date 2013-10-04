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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * AddAuthorizationRefresherCompilerPass allow to add an authorization refresher.
 *
 * @author Thomas Prelot
 */
class AddAuthorizationRefresherCompilerPass implements CompilerPassInterface
{
    /**
     * Process the ContainerBuilder to add the default oauth authorization refresher.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(
            'da_api_client.api_implementor'
        );

        foreach ($taggedServices as $id => $attributes) {
            $definition = $container->getDefinition($id);

           	// If the DaOAuthClientBundle is loaded, use its authorization refresher.
            if ($container->hasDefinition('da_oauth_client.authorization_refresher.oauth')) {
                $definition->addMethodCall(
                	'setAuthorizationRefresher', 
                	array(new Reference('da_oauth_client.authorization_refresher.oauth'))
                );
            }
        }
    }
}
