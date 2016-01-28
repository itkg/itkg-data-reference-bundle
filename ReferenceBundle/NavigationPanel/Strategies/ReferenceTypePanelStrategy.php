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
     * @return string
     */
    protected function getTemplate()
    {
        return "ItkgReferenceBundle:AdministrationPanel:".$this->name.".html.twig";
    }
}
