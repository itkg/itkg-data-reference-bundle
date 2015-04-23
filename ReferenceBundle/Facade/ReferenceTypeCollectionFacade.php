<?php

namespace Itkg\ReferenceBundle\Facade;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\DeletedFacade;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class ReferenceTypeCollectionFacade
 */
class ReferenceTypeCollectionFacade implements FacadeInterface
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'reference_types';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\ContentTypeFacade>")
     */
    protected $referenceTypes = array();

    /**
     * @param FacadeInterface|ContentTypeFacade $facade
     */
    public function addReferenceType(FacadeInterface $facade)
    {
        $this->referenceTypes[] = $facade;
    }
}
