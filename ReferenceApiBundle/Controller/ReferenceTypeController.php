<?php
namespace Itkg\ReferenceApiBundle\Controller;

use Itkg\ReferenceInterface\Event\ReferenceTypeEvent;
use Itkg\ReferenceInterface\ReferenceTypeEvents;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class ReferenceTypeController
 *
 * @Config\Route("reference-type")
 *
 * @Api\Serialize()
 */
class ReferenceTypeController extends BaseController
{
    /**
     * @param string $referenceTypeId
     *
     * @return FacadeInterface
     *
     * @Config\Route("/{referenceTypeId}", name="open_orchestra_api_reference_type_show")
     * @Config\Method({"GET"})
     *
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::FIELD_TYPES
     * })
     */
    public function showAction($referenceTypeId)
    {
        $referenceType = $this->get('itkg_reference.repository.reference_type')->findOneByReferenceTypeIdInLastVersion($referenceTypeId);

        return $this->get('open_orchestra_api.transformer_manager')->get('reference_type')->transform($referenceType);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_reference_type_list")
     * @Config\Method({"GET"})
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS
     * })
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
//         $this->denyAccessUnlessGranted(ContributionActionInterface::READ, ReferenceTypeInterface::ENTITY_TYPE);
        $mapping = array(
            'name' => 'names',
            'reference_type_id' => 'referenceTypeId'
        );
        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);
        $repository = $this->get('itkg_reference.repository.reference_type');

        $collection = $repository->findAllNotDeletedInLastVersionForPaginate($configuration);
        $recordsTotal = $repository->countByReferenceTypeInLastVersion();
        $recordsFiltered = $repository->countNotDeletedInLastVersionWithSearchFilter($configuration);
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('reference_type_collection');
        $facade = $collectionTransformer->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/delete-multiple", name="open_orchestra_api_reference_type_delete_multiple")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteReferenceTypesAction(Request $request)
    {
        $format = $request->get('_format', 'json');

        $facade = $this->get('jms_serializer')->deserialize(
            $request->getReference(),
            $this->getParameter('open_orchestra_api.facade.reference_type_collection.class'),
            $format
        );
        $referenceTypeRepository = $this->get('itkg_reference.repository.reference_type');
        $referenceTypes = $this->get('open_orchestra_api.transformer_manager')->get('reference_type_collection')->reverseTransform($facade);
        $referenceTypeIds = array();
        foreach ($referenceTypes as $referenceType) {
            if ($this->isGranted(ContributionActionInterface::DELETE, $referenceType) &&
                0 == $this->get('itkg_reference.repository.reference')->countByReferenceType($referenceType->getReferenceTypeId())
            ) {
                $referenceTypeIds[] = $referenceType->getReferenceTypeId();
                $this->dispatchEvent(ReferenceTypeEvents::CONTENT_TYPE_DELETE, new ReferenceTypeEvent($referenceType));
            }
        }
        $referenceTypeRepository->removeByReferenceTypeId($referenceTypeIds);

        return array();
    }

    /**
     * @Config\Route("/reference/reference-type-list", name="open_orchestra_api_reference_type_list_for_reference")
     * @Config\Method({"GET"})
     * @return FacadeInterface
     */
    public function listForReferenceAction()
    {
        $repository = $this->get('itkg_reference.repository.reference_type');

        $collection = $repository->findAllNotDeletedInLastVersion();
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('reference_type_collection');
        $facade = $collectionTransformer->transform($collection);

        return $facade;
    }

    /**
     * @param string $referenceTypeId
     *
     * @Config\Route("/{referenceTypeId}/delete", name="open_orchestra_api_reference_type_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($referenceTypeId)
    {
        if (0 == $this->get('itkg_reference.repository.reference')->countByReferenceType($referenceTypeId)) {
            $referenceTypes = $this->get('itkg_reference.repository.reference_type')->findBy(array('referenceTypeId' => $referenceTypeId));
            if (count($referenceTypes) > 0) {
                foreach ($referenceTypes as $referenceType) {
                    $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $referenceType);
                }
                $this->get('itkg_reference_bundle.manager.reference_type')->delete($referenceTypes);
                $this->dispatchEvent(ReferenceTypeEvents::CONTENT_TYPE_DELETE, new ReferenceTypeEvent($referenceTypes[0]));
                $this->get('object_manager')->flush();
            }
        }

        return array();
    }
}
