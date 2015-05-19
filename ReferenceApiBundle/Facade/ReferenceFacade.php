<?php

namespace Itkg\ReferenceApiBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\DeletedFacade;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class ReferenceFacade
 */
class ReferenceFacade extends DeletedFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $referenceId;

    /**
     * @Serializer\Type("string")
     */
    public $referenceType;

    /**
     * @Serializer\Type("string")
     */
    public $language;

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\ContentAttributeFacade>")
     */
    protected $attributes = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addAttribute(FacadeInterface $facade)
    {
        $this->attributes[] = $facade;
    }
}