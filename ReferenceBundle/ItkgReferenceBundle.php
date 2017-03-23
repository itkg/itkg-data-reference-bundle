<?php

namespace Itkg\ReferenceBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Itkg\ReferenceBundle\DependencyInjection\Compiler\RoleCompilerPass;

/**
 * Class ItkgReferenceBundle
 */
class ItkgReferenceBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
//         $container->addCompilerPass(new RoleCompilerPass());
    }
}
