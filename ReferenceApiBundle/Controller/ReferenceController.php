<?php

namespace Itkg\ReferenceApiBundle\Controller;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\ReferenceNotDeletableException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use Itkg\ReferenceInterface\Model\ReferenceInterface;
use Itkg\ReferenceInterface\Event\ReferenceEvent;
use Itkg\ReferenceInterface\Model\ReferenceTypeInterface;
use Itkg\ReferenceApiBundle\Exceptions\HttpException\ReferenceNotFoundHttpException;
use Itkg\ReferenceInterface\ReferenceEvents;
use Itkg\ReferenceInterface\Event\ReferenceDeleteEvent;

/**
 * Class ReferenceController
 *
 * @Config\Route("reference")
 *
 * @Api\Serialize()
 */
class ReferenceController extends BaseController
{
    /**
     * @param string $referenceId
     * @param string $language
     *
     * @Config\Route(
     *     "/show/{referenceId}/{language}",
     *     name="open_orchestra_api_reference_show",
     * )
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     * @throws ReferenceNotFoundHttpException
     */
    public function showAction($referenceId, $language)
    {
        if (null === $language) {
            $language = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteDefaultLanguage();
        }
        $reference = $this->findOneReference($referenceId, $language);

        if (!$reference) {
            throw new ReferenceNotFoundHttpException();
        }

        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, $reference);

        return $this->get('open_orchestra_api.transformer_manager')->get('reference')->transform($reference);
    }

    /**
     * @param Request $request
     * @param string  $referenceTypeId
     * @param string  $language
     *
     * @Config\Route("/list/{referenceTypeId}/{language}", name="open_orchestra_api_reference_list")
     * @Config\Method({"GET"})
     *
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS
     * })
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request, $referenceTypeId, $language)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, ReferenceInterface::ENTITY_TYPE);

        $referenceType = $this->get('itkg_reference.repository.reference_type')->findOneByReferenceTypeIdInLastVersion($referenceTypeId);

        $mapping = $this->getMappingReferenceType($language, $referenceType);

        $searchTypes = array();
        foreach ($referenceType->getFields() as $field) {
            $searchTypes['attributes.' . $field->getFieldId()] = $field->getFieldTypeSearchable();
        }

        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);

        $repository =  $this->get('itkg_reference.repository.reference');

        $collection = $repository->findForPaginateFilterByReferenceTypeAndLanguage($configuration, $referenceTypeId, $language, $searchTypes);
        $recordsTotal = $repository->countFilterByReferenceTypeAndLanguage($referenceTypeId, $language);
        $recordsFiltered = $repository->countWithFilterAndReferenceTypeAndLanguage($configuration, $referenceTypeId, $language, $searchTypes);
        $facade = $this->get('open_orchestra_api.transformer_manager')->get('reference_collection')->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/duplicate", name="open_orchestra_api_reference_duplicate")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function duplicateAction(Request $request)
    {
        $format = $request->get('_format', 'json');
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getReference(),
            $this->getParameter('open_orchestra_api.facade.reference.class'),
            $format
        );
        $reference = $this->get('open_orchestra_api.transformer_manager')->get('reference')->reverseTransform($facade);
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $reference);

        $frontLanguages = $this->getParameter('open_orchestra_backoffice.orchestra_choice.front_language');

        $referenceId = $reference->getReferenceId();
        $newReferenceId = null;
        $objectManager = $this->get('object_manager');

        foreach (array_keys($frontLanguages) as $language) {
            $reference = $this->findOneReference($referenceId, $language);
            if ($reference instanceof ReferenceInterface) {
                $duplicateReference = $this->get('itkg_reference.manager.reference')->duplicateReference($reference, $newReferenceId);
                $objectManager->persist($duplicateReference);

                $newReferenceId = $duplicateReference->getReferenceId();
                $this->dispatchEvent(ReferenceEvents::REFERENCE_DUPLICATE, new ReferenceEvent($duplicateReference));
            }
        }
        $objectManager->flush();

        return array();
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/delete-multiple", name="open_orchestra_api_reference_delete_multiple")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteReferencesAction(Request $request)
    {
        $format = $request->get('_format', 'json');
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('itkg_reference.facade.reference_collection.class'),
            $format
        );
        $references = $this->get('open_orchestra_api.transformer_manager')->get('reference_collection')->reverseTransform($facade);
        $repository = $this->get('itkg_reference.repository.reference');

        foreach ($references as $reference) {
            $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $reference);
            $referenceId = $reference->getReferenceId();
            if ($this->isGranted(ContributionActionInterface::DELETE, $reference)) {
                $repository->softDeleteReference($referenceId);
                $this->dispatchEvent(ReferenceEvents::REFERENCE_DELETE, new ReferenceDeleteEvent($referenceId));
            }
        }

        return array();
    }

    /**
     * @param string $referenceId
     *
     * @Config\Route("/delete/{referenceId}", name="open_orchestra_api_reference_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     * @throws ReferenceNotDeletableException
     */
    public function deleteAction($referenceId)
    {
        $repository = $this->get('itkg_reference.repository.reference');
        $reference = $repository->findOneByReferenceId($referenceId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $reference);

        $repository->softDeleteReference($referenceId);
        $this->dispatchEvent(ReferenceEvents::REFERENCE_DELETE, new ReferenceDeleteEvent($referenceId));

        return array();
    }

    /**
     * @param string $referenceId
     * @param string $language
     *
     * @Config\Route("/new-language/{referenceId}/{language}", name="open_orchestra_api_reference_new_language")
     * @Config\Method({"POST"})
     *
     * @return Response
     * @throws ReferenceNotFoundHttpException
     */
    public function newLanguageAction($referenceId, $language)
    {
        $reference = $this->get('itkg_reference.repository.reference')->findById($referenceId);

        if (!$reference instanceof ReferenceInterface) {
            throw new ReferenceNotFoundHttpException();
        }

        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $reference);

        $newReference = $this->get('itkg_reference.manager.reference')->duplicateReference($reference);
        $newReference->setLanguage($language);
        $objectManager = $this->get('object_manager');
        $objectManager->persist($newReference);
        $objectManager->flush();
        $this->dispatchEvent(ReferenceEvents::REFERENCE_DUPLICATE, new ReferenceEvent($newReference));

        return array();
    }

    /**
     * @param string   $referenceId
     * @param string   $language
     *
     * @return null|ReferenceInterface
     */
    protected function findOneReference($referenceId, $language)
    {
        $referenceRepository = $this->get('itkg_reference.repository.reference');
        $reference = $referenceRepository->findOneByLanguage($referenceId, $language);

        return $reference;
    }

    /**
     * @param string                 $language
     * @param ReferenceTypeInterface $referenceType
     *
     * @return array
     */
    protected function getMappingReferenceType($language, ReferenceTypeInterface $referenceType)
    {
        $mapping = array(
            'name' => 'name',
            'created_at' => 'createdAt',
            'created_by' => 'createdBy',
            'updated_at' => 'updatedAt',
            'updated_by' => 'updatedBy',
        );
        foreach ($referenceType->getDefaultListable() as $column => $isListable) {
            if (!$isListable) {
                unset($mapping[$column]);
            }
        }
        foreach ($referenceType->getFields() as $field) {
            $mapping['fields.' . $field->getFieldId() . '.string_value'] = 'attributes.' .     $field->getFieldId() . '.stringValue';
        }

        return $mapping;
    }
}
