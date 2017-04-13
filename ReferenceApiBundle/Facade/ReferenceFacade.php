<?php

namespace Itkg\ReferenceApiBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\DeletedFacade;
use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\Traits\BlameableFacade;

/**
 * Class ReferenceFacade
 */
class ReferenceFacade extends DeletedFacade
{
    use BlameableFacade;

    /**
     * @Serializer\Type("string")
     */
    public $id;

    /**
     * @Serializer\Type("string")
     */
    public $referenceId;

    /**
     * @Serializer\Type("string")
     */
    public $referenceTypeId;

    /**
     * @Serializer\Type("string")
     */
    public $version;

    /**
     * @Serializer\Type("string")
     */
    public $versionName;

    /**
     * @Serializer\Type("string")
     */
    public $language;

    /**
     * @Serializer\Type("OpenOrchestra\WorkflowAdminBundle\Facade\StatusFacade")
     */
    public $status;

    /**
     * @Serializer\Type("string")
     */
    public $statusLabel;

    /**
     * @Serializer\Type("string")
     */
    public $statusId;

    /**
     * @Serializer\Type("boolean")
     */
    public $linkedToSite;

    /**
     * @Serializer\Type("boolean")
     */
    public $used;

    /**
     * @Serializer\Type("array<string,OpenOrchestra\ApiBundle\Facade\ContentAttributeFacade>")
     */
    protected $attributes = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addAttribute(FacadeInterface $facade)
    {
        $this->attributes[$facade->name] = $facade;
    }
}
