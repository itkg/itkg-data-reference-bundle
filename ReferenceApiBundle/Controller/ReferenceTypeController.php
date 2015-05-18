<?php
namespace Itkg\ReferenceApiBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class ReferenceTypeController
 */
class ReferenceTypeController extends BaseController
{
    /**
     * @Config\Route("/reference-type/list", name="open_orchestra_api_reference_type_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REFERENCE_TYPE')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $referenceTypeCollection = $this->get('itkg_reference.repository.reference_type')->findAllByNotDeleted();

        return $this->get('open_orchestra_api.transformer_manager')->get('reference_type_collection')->transform($referenceTypeCollection);
    }
}
