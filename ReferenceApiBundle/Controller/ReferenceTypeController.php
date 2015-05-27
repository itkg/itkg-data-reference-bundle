<?php
namespace Itkg\ReferenceApiBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Itkg\ReferenceInterface\Event\ReferenceTypeEvent;
use Itkg\ReferenceInterface\ReferenceTypeEvents;
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
     * @param string $referenceTypeId
     *
     * @Config\Route("/{referenceTypeId}", name="open_orchestra_api_reference_type_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REFERENCE_TYPE')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($referenceTypeId)
    {
        $referenceType = $this->get('itkg_reference.repository.reference_type')->findOneByReferenceTypeId($referenceTypeId);

        return $this->get('open_orchestra_api.transformer_manager')->get('reference_type')->transform($referenceType);
    }

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

    /**
     * @param string $referenceTypeId
     *
     * @Config\Route("/{referenceTypeId}/delete", name="open_orchestra_api_reference_type_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REFERENCE_TYPE')")
     *
     * @return Response
     */
    public function deleteAction($referenceTypeId)
    {
        $referenceTypes = $this->get('itkg_reference.repository.reference_type')->findBy(array('referenceTypeId' => $referenceTypeId));
        $this->get('itkg_reference.manager.reference_type')->delete($referenceTypes);
        $this->dispatchEvent(ReferenceTypeEvents::REFERENCE_TYPE_DELETE, new ReferenceTypeEvent(current($referenceTypes)));
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response('', 200);
    }
}
