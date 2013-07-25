<?php

namespace Da\ApiClientBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Da\ApiClientBundle\DependencyInjection\Compiler\ResolveApiClientCompilerPass;

class DaApiClientBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        
        $container->addCompilerPass(new ResolveApiClientCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);
    }
}
