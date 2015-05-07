<?php

namespace Itkg\ReferenceApiBundle\Facade;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\AbstractFacade;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class ReferenceCollectionFacade
 */
class ReferenceCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'references';

    /**
     * @Serializer\Type("array<Itkg\ReferenceApiBundle\Facade\ReferenceFacade>")
     */
    protected $references = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addReference(FacadeInterface $facade)
    {
        $this->references[] = $facade;
    }
}
