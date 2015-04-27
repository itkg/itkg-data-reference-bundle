<?php

namespace Itkg\ReferenceBundle\Facade;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\AbstractFacade;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class ReferenceTypeCollectionFacade
 */
class ReferenceTypeCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'reference_types';

    /**
     * @Serializer\Type("array<Itkg\ReferenceBundle\Facade\ReferenceTypeFacade>")
     */
    protected $referenceTypes = array();

    /**
     * @param FacadeInterface|ReferenceTypeFacade $facade
     */
    public function addReferenceType(FacadeInterface $facade)
    {
        $this->referenceTypes[] = $facade;
    }
}
