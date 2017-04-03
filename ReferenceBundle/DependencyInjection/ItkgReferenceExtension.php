<?php

namespace Itkg\ReferenceBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Itkg\ReferenceBundle\Security\ReferenceRoleInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ItkgReferenceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $configurationRoles = array(
            'open_orchestra_backoffice.role.administration' => array(
                'thirdpackage' => array(
                    'reference' => array(
                        ReferenceRoleInterface::REFERENCE_ADMIN => array()
                    )
                ),
            ),
        );
        if ($container->hasParameter('open_orchestra_backoffice.configuration.roles')) {
            $configurationRoles = array_merge_recursive($container->getParameter('open_orchestra_backoffice.configuration.roles'), $configurationRoles);
        }
        $container->setParameter('open_orchestra_backoffice.configuration.roles', $configurationRoles);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('manager.yml');
        $loader->load('form.yml');
        $loader->load('subscriber.yml');
        $loader->load('voter.yml');
    }
}
