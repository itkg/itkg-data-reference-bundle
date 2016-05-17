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

        if ($container->hasParameter('open_orchestra_backoffice.role')) {
            $param = $container->getParameter('open_orchestra_backoffice.role');
            if ($container->hasParameter('itkg_reference.role')) {
                $param = array_merge_recursive($param, $container->getParameter('itkg_reference.role'));
            }
            $container->setParameter('open_orchestra_backoffice.role', $param);
        }
    }
}
