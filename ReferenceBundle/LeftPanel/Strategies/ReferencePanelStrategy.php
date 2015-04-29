<?php

namespace Itkg\ReferenceBundle\LeftPanel\Strategies;

use OpenOrchestra\Backoffice\LeftPanel\Strategies\AdministrationPanelStrategy;

/**
 * Class ReferencePanelStrategy
 */

class ReferencePanelStrategy extends AdministrationPanelStrategy
{
    /**
     * @param string $name
     * @param string $role
     * @param string $bundle
     * @param int    $weight
     * @param string $parent
     */

    protected $path;

    public function __construct($name, $role, $bundle, $weight = 0, $parent = self::ADMINISTRATION)
    {
        $this->name = $name;
        $this->role = $role;
        $this->weight = $weight;
        $this->parent = $parent;
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
