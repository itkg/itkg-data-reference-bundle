<?php
namespace Itkg\ReferenceApiBundle\Controller;

use Itkg\ReferenceInterface\Event\ReferenceTypeEvent;
use Itkg\ReferenceInterface\ReferenceTypeEvents;
use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ReferenceTypeController
 *
 * @Config\Route("/reference-type")
 */
class ReferenceTypeController extends BaseController
{
    use HandleRequestDataTable;

    /**
     * @param Request $request
     *
     * @Config\Route("/list", name="open_orchestra_api_reference_type_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_REFERENCE_TYPE')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $repository = $this->get('itkg_reference.repository.reference_type');
        $transformer = $this->get('open_orchestra_api.transformer_manager')->get('reference_type_collection');

        if ($request->get('entityId')) {
            $element = $repository->find($request->get('entityId'));
            return $transformer->transform(array($element));
        }

        $configuration = PaginateFinderConfiguration::generateFromRequest($request);
        $mapping = $this
            ->get('open_orchestra.annotation_search_reader')
            ->extractMapping($this->container->getParameter('itkg_reference.document.reference_type.class'));
        $configuration->setDescriptionEntity($mapping);
        $referenceTypeCollection = $repository->findAllNotDeletedInLastVersionForPaginate($configuration);
        $recordsTotal = $repository->countByContentTypeInLastVersion();
        $recordsFiltered = $repository->countNotDeletedInLastVersionWithSearchFilter($configuration);

        return $this->generateFacadeDataTable($transformer, $referenceTypeCollection, $recordsTotal, $recordsFiltered);
    }

    /**
     * @param string $referenceTypeId
     *
     * @Config\Route("/{referenceTypeId}/delete", name="open_orchestra_api_reference_type_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_REFERENCE_TYPE')")
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

    /**
     * @param string $referenceTypeId
     *
     * @Config\Route("/{referenceTypeId}", name="open_orchestra_api_reference_type_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_REFERENCE_TYPE')")
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
}
