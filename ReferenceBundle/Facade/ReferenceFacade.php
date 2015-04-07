<?php

namespace Itkg\ReferenceBundle\Facade;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\DeletedFacade;
use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\ApiBundle\Facade\Traits\TimestampableFacade as Timestampable;

/**
 * Class ReferenceFacade
 */
class ReferenceFacade extends DeletedFacade
{
    //use Timestampable;
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
