<?php

namespace Itkg\ReferenceBundle\DependencyInjection\Compiler;

use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\AbstractRoleCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Itkg\ReferenceBundle\NavigationPanel\Strategies\ReferenceTypeForReferencePanelStrategy;
use Itkg\ReferenceBundle\NavigationPanel\Strategies\ReferenceTypePanelStrategy;

/**
 * Class RoleCompilerPass
 */
class RoleCompilerPass extends AbstractRoleCompilerPass
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $this->addRoles($container, array(
            ReferenceTypeForReferencePanelStrategy::ROLE_ACCESS_REFERENCE_TYPE_FOR_REFERENCE,
            ReferenceTypePanelStrategy::ROLE_ACCESS_REFERENCE_TYPE,
        ));
    }
}
