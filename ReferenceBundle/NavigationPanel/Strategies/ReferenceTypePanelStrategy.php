<?php

namespace Itkg\ReferenceBundle\NavigationPanel\Strategies;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;

/**
 * Class ReferenceTypePanelStrategy
 */
class ReferenceTypePanelStrategy extends AdministrationPanelStrategy
{
    const ROLE_ACCESS_REFERENCE_TYPE = 'ROLE_ACCESS_REFERENCE_TYPE';

    /**
     * @param string $name
     * @param string $role
     * @param string $bundle
     * @param int    $weight
     * @param string $parent
     */
    public function __construct($name, $role, $bundle, $weight = 0, $parent = 'administration')
    {
        parent::__construct($name, $role, $weight, $parent);

        $this->bundle = $bundle;
    }

    /**
     * return the link setted in the associated twig file
     * 
     * @return string
     */
    public function show()
    {
        return $this->render($this->bundle.":AdministrationPanel:".$this->name.".html.twig");
    }
}
