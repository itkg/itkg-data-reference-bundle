<?php

namespace Itkg\ReferenceApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\Event\ManagerEventArgs;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\ApiBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use OpenOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class ReferenceController
 *
 * @Route("reference")
 */
class ReferenceController extends BaseController
{
    /**
     * @param string  $referenceId
     *
     * @Route("/{referenceId}", name="open_orchestra_api_itkg_reference_getId")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function getIdAction($referenceId)
    {
        $reference = $this->get("itkg_reference.repository.reference")->findOneByIdAndLanguageNotDeleted($referenceId);

        $facadeReference = $this->get('open_orchestra_api.transformer_manager')->get('reference')->transform($reference);

        return $facadeReference;
    }

    /**
     * @Config\Route("/reference-type/list", name="open_orchestra_api_reference_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REFERENCE_TYPE')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $referenceType = $request->get('reference_type');
        $referenceTypeCollection = $this->get('itkg_reference.repository.reference')->findByReferenceType($referenceType);

        $facade = $this->get('open_orchestra_api.transformer_manager')->get('reference_collection')->transform($referenceTypeCollection, $referenceType);
        
        return $facade;
    }
}
