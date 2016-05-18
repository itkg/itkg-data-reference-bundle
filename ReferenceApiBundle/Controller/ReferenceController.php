<?php

namespace Itkg\ReferenceApiBundle\Controller;

use Itkg\ReferenceApiBundle\Facade\ReferenceCollectionFacade;
use Itkg\ReferenceInterface\ReferenceEvents;
use Itkg\ReferenceInterface\Event\ReferenceEvent;
use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class ReferenceController
 *
 * @Config\Route("reference")
 */
class ReferenceController extends BaseController
{
    use HandleRequestDataTable;

    /**
     * @param Request $request
     *
     * @Config\Route("/reference-type/list", name="open_orchestra_api_reference_list")
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
        $mappingEntity = $this->get('open_orchestra.annotation_search_reader')->extractMapping('Itkg\ReferenceModelBundle\Document\Reference');
        $configuration = PaginateFinderConfiguration::generateFromRequest($request);
        $configuration->setDescriptionEntity($mappingEntity);

        $referenceType = $request->get('reference_type');
        $referenceTypeCollection = $this->get('itkg_reference.repository.reference')->findByReferenceTypeNotDeletedWithPagination($configuration, $referenceType);

        /** @var ReferenceCollectionFacade $facade */
        $facade = $this->get('open_orchestra_api.transformer_manager')->get('reference_collection')->transform($referenceTypeCollection, $referenceType);

        $facade->recordsTotal = $this->get('itkg_reference.repository.reference')->countByReferenceTypeNotDeleted($referenceType);
        $facade->recordsFiltered = count($referenceTypeCollection);

        return $facade;
    }

    /**
     * @param string $referenceId
     *
     * @Config\Route("/{referenceId}/delete", name="open_orchestra_api_reference_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_REFERENCE_TYPE_FOR_REFERENCE')")
     *
     * @return Response
     */
    public function deleteAction($referenceId)
    {
        $reference = $this->get('itkg_reference.repository.reference')->findOneByReferenceId($referenceId);

        $reference->setDeleted(true);
        $this->get('doctrine.odm.mongodb.document_manager')->flush();
        $this->dispatchEvent(ReferenceEvents::REFERENCE_DELETE, new ReferenceEvent($reference));

        return new Response('', 200);
    }

    /**
     * @param Request $request
     * @param string  $referenceId
     *
     * @Config\Route("/{referenceId}", name="open_orchestra_api_reference_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_REFERENCE_TYPE_FOR_REFERENCE')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction(Request $request, $referenceId)
    {
        $language = $request->get('language');

        $referenceRepository = $this->get('itkg_reference.repository.reference');
        $reference = $referenceRepository->findOneByReferenceIdAndLanguage($referenceId, $language);

        if (!$reference) {
            $oldReference = $referenceRepository->findOneByReferenceId($referenceId);

            if (!$oldReference) {
                throw new HttpException(500, 'No reference Found');
            }

            $reference = $this->get('itkg_reference.manager.reference')->createNewLanguageReference($oldReference, $language);
            $dm = $this->get('doctrine.odm.mongodb.document_manager');
            $dm->persist($reference);
            $dm->flush($reference);
        }

        return $this->get('open_orchestra_api.transformer_manager')->get('reference')->transform($reference);
    }
}
