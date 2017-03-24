<?php

namespace Itkg\ReferenceApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\ListStatus;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\ReferenceNotDeletableException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\ReferenceNotFoundHttpException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\StatusChangeNotGrantedHttpException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use Itkg\ReferenceInterface\Model\ReferenceInterface;
use Itkg\ReferenceInterface\Event\ReferenceEvent;
use Itkg\ReferenceInterface\Model\ReferenceTypeInterface;

/**
 * Class ReferenceController
 *
 * @Config\Route("reference")
 *
 * @Api\Serialize()
 */
class ReferenceController extends BaseController
{
    use ListStatus;

    /**
     * @param string  $referenceId
     * @param string  $version
     * @param string  $language
     *
     * @Config\Route(
     *     "/show/{referenceId}/{language}/{version}",
     *     name="open_orchestra_api_reference_show",
     *     defaults={"version": null, "language": null},
     * )
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     * @throws ReferenceNotFoundHttpException
     */
    public function showAction($referenceId, $language, $version)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, SiteInterface::ENTITY_TYPE);
        if (null === $language) {
            $language = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteDefaultLanguage();
        }
        $reference = $this->findOneReference($referenceId, $language, $version);
        if (!$reference) {
            throw new ReferenceNotFoundHttpException();
        }

        return $this->get('open_orchestra_api.transformer_manager')->get('reference')->transform($reference);
    }

    /**
     * @param Request $request
     * @param string  $referenceTypeId
     * @param string  $siteId
     * @param string  $language
     *
     * @Config\Route("/list/{referenceTypeId}/{siteId}/{language}", name="open_orchestra_api_reference_list")
     * @Config\Method({"GET"})
     *
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS
     * })
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request, $referenceTypeId, $siteId, $language)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, SiteInterface::ENTITY_TYPE);

        $referenceType = $this->get('itkg_reference.repository.reference_type')->findOneByReferenceTypeIdInLastVersion($referenceTypeId);
        $mapping = $this->getMappingReferenceType($language, $referenceType);

        $searchTypes = array();
        foreach ($referenceType->getFields() as $field) {
            $searchTypes['attributes.' . $field->getFieldId()] = $field->getFieldTypeSearchable();
        }

        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);

        $repository =  $this->get('itkg_reference.repository.reference');

        $collection = $repository->findForPaginateFilterByReferenceTypeSiteAndLanguage($configuration, $referenceTypeId, $siteId, $language, $searchTypes);
        $recordsTotal = $repository->countFilterByReferenceTypeSiteAndLanguage($referenceTypeId, $siteId, $language);
        $recordsFiltered = $repository->countWithFilterAndReferenceTypeSiteAndLanguage($configuration, $referenceTypeId, $siteId, $language, $searchTypes);
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
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, ReferenceInterface::ENTITY_TYPE);

        $format = $request->get('_format', 'json');
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getReference(),
            $this->getParameter('open_orchestra_api.facade.reference.class'),
            $format
            );
        $reference = $this->get('open_orchestra_api.transformer_manager')->get('reference')->reverseTransform($facade);
        $frontLanguages = $this->getParameter('open_orchestra_backoffice.orchestra_choice.front_language');

        $referenceId = $reference->getReferenceId();
        $newReferenceId = null;
        $objectManager = $this->get('object_manager');

        foreach (array_keys($frontLanguages) as $language) {
            $reference = $this->findOneReference($referenceId, $language);
            if ($reference instanceof ReferenceInterface) {
                $duplicateReference = $this->get('open_orchestra_backoffice.manager.reference')->duplicateReference($reference, $newReferenceId);
                $objectManager->persist($duplicateReference);

                $newReferenceId = $duplicateReference->getReferenceId();
                $this->dispatchEvent(ReferenceEvents::CONTENT_DUPLICATE, new ReferenceEvent($duplicateReference));
            }
        }
        $objectManager->flush();

        return array();
    }

    /**
     * @param Request $request
     * @param string  $referenceId
     * @param string  $language
     *
     * @Config\Route("/delete-multiple-version/{referenceId}/{language}", name="open_orchestra_api_reference_delete_multiple_versions")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteReferenceVersionsAction(Request $request, $referenceId, $language)
    {
        $format = $request->get('_format', 'json');
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getReference(),
            $this->getParameter('open_orchestra_api.facade.reference_collection.class'),
            $format
            );
        $references = $this->get('open_orchestra_api.transformer_manager')->get('reference_collection')->reverseTransform($facade);
        $versionsCount = $this->get('open_orchestra_model.repository.reference')->countNotDeletedByLanguage($referenceId, $language);
        if ($versionsCount > count($references)) {
            $storageIds = array();
            foreach ($references as $reference) {
                if ($this->isGranted(ContributionActionInterface::DELETE, $reference) && !$reference->getStatus()->isPublishedState()) {
                    $storageIds[] = $reference->getId();
                    $this->dispatchEvent(ReferenceEvents::CONTENT_DELETE_VERSION, new ReferenceEvent($reference));
                }
            }
            $this->get('open_orchestra_model.repository.reference')->removeReferenceVersion($storageIds);
        }

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
            $request->getReference(),
            $this->getParameter('open_orchestra_api.facade.reference_collection.class'),
            $format
            );
        $references = $this->get('open_orchestra_api.transformer_manager')->get('reference_collection')->reverseTransform($facade);
        $repository = $this->get('open_orchestra_model.repository.reference');

        foreach ($references as $reference) {
            $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $reference);
            $referenceId = $reference->getReferenceId();
            if (
                false === $repository->hasReferenceIdWithoutAutoUnpublishToState($referenceId) &&
                $this->isGranted(ContributionActionInterface::DELETE, $reference)
                ) {
                    $repository->softDeleteReference($referenceId);
                    $this->dispatchEvent(ReferenceEvents::CONTENT_DELETE, new ReferenceDeleteEvent($referenceId, $reference->getSiteId()));
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
        $repository = $this->get('open_orchestra_model.repository.reference');
        $reference = $repository->findOneByReferenceId($referenceId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $reference);

        if (true === $repository->hasReferenceIdWithoutAutoUnpublishToState($referenceId)) {
            throw new ReferenceNotDeletableException();
        }

        $repository->softDeleteReference($referenceId);
        $this->dispatchEvent(ReferenceEvents::CONTENT_DELETE, new ReferenceDeleteEvent($referenceId, $reference->getSiteId()));

        return array();
    }

    /**
     * @param boolean|null $published
     *
     * @Config\Route("/list/not-published-by-author", name="open_orchestra_api_reference_list_author_and_site_not_published", defaults={"published": false})
     * @Config\Route("/list/by-author", name="open_orchestra_api_reference_list_author_and_site", defaults={"published": null})
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function listReferenceByAuthorAndSiteIdAction($published)
    {
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $reference = $this->get('open_orchestra_model.repository.reference')->findByHistoryAndSiteId(
            $user->getId(),
            $siteId,
            array(ReferenceEvents::CONTENT_CREATION, ReferenceEvents::CONTENT_UPDATE),
            $published,
            10,
            array('histories.updatedAt' => -1)
            );

        return $this->get('open_orchestra_api.transformer_manager')->get('reference_collection')->transform($reference);
    }

    /**
     * @param Request $request
     * @param string  $referenceId
     * @param string  $language
     * @param string  $originalVersion
     *
     * @Config\Route("/new-version/{referenceId}/{language}/{originalVersion}", name="open_orchestra_api_reference_new_version")
     * @Config\Method({"POST"})
     *
     * @return Response
     * @throws ReferenceNotFoundHttpException
     */
    public function newVersionAction(Request $request, $referenceId, $language, $originalVersion)
    {
        /** @var ReferenceInterface $reference */
        $reference = $this->findOneReference($referenceId, $language, $originalVersion);
        if (!$reference instanceof ReferenceInterface) {
            throw new ReferenceNotFoundHttpException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $reference);

        $facade = $this->get('jms_serializer')->deserialize(
            $request->getReference(),
            'OpenOrchestra\ApiBundle\Facade\ReferenceFacade',
            $request->get('_format', 'json')
            );
        $newReference = $this->get('open_orchestra_backoffice.manager.reference')->newVersionReference($reference, $facade->versionName);

        $objectManager = $this->get('object_manager');
        $objectManager->persist($newReference);
        $objectManager->flush();
        $this->dispatchEvent(ReferenceEvents::CONTENT_DUPLICATE, new ReferenceEvent($newReference));

        return array();
    }

    /**
     * @param string  $referenceId
     * @param string  $language
     *
     * @Config\Route("/new-language/{referenceId}/{language}", name="open_orchestra_api_reference_new_language")
     * @Config\Method({"POST"})
     *
     * @return Response
     * @throws ReferenceNotFoundHttpException
     */
    public function newLanguageAction($referenceId, $language)
    {
        $reference = $this->get('open_orchestra_model.repository.reference')->findLastVersion($referenceId);
        if (!$reference instanceof ReferenceInterface) {
            throw new ReferenceNotFoundHttpException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $reference);

        $newReference = $this->get('open_orchestra_backoffice.manager.reference')->newVersionReference($reference);
        $status = $this->get('open_orchestra_model.repository.status')->findOneByTranslationState();
        $newReference->setStatus($status);
        $newReference->setLanguage($language);
        $objectManager = $this->get('object_manager');
        $objectManager->persist($newReference);
        $objectManager->flush();
        $this->dispatchEvent(ReferenceEvents::CONTENT_DUPLICATE, new ReferenceEvent($newReference));

        return array();
    }

    /**
     * @param string  $referenceId
     * @param string  $language
     *
     * @Config\Route("/list-version/{referenceId}/{language}", name="open_orchestra_api_reference_list_version")
     * @Config\Method({"GET"})
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS_DELETE_VERSION
     * })
     * @return Response
     */
    public function listVersionAction($referenceId, $language)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, SiteInterface::ENTITY_TYPE);
        $references = $this->get('open_orchestra_model.repository.reference')->findNotDeletedSortByUpdatedAt($referenceId, $language);

        return $this->get('open_orchestra_api.transformer_manager')->get('reference_collection')->transform($references);
    }

    /**
     * @param string $referenceId
     * @param string $language
     * @param string $version
     *
     * @Config\Route(
     *     "/list-statuses/{referenceId}/{language}/{version}",
     *     name="open_orchestra_api_reference_list_status")
     * @Config\Method({"GET"})
     *
     * @return Response
     * @throws ReferenceNotFoundHttpException
     */
    public function listStatusesForReferenceAction($referenceId, $language, $version)
    {
        $reference = $this->findOneReference($referenceId, $language, $version);
        if (!$reference instanceof ReferenceInterface) {
            throw new ReferenceNotFoundHttpException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, $reference);

        return $this->listStatuses($reference);
    }

    /**
     * @param Request $request
     * @param boolean $saveOldPublishedVersion
     *
     * @Config\Route(
     *     "/update-status",
     *     name="open_orchestra_api_reference_update_status",
     *     defaults={"saveOldPublishedVersion": false},
     * )
     * @Config\Route(
     *     "/update-status-with-save-published-version",
     *     name="open_orchestra_api_reference_update_status_with_save_published",
     *     defaults={"saveOldPublishedVersion": true},
     * )
     * @Config\Method({"PUT"})
     *
     * @return Response
     * @throws ReferenceNotFoundHttpException
     * @throws StatusChangeNotGrantedHttpException
     */
    public function changeStatusAction(Request $request, $saveOldPublishedVersion)
    {
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getReference(),
            'OpenOrchestra\ApiBundle\Facade\ReferenceFacade',
            $request->get('_format', 'json')
            );

        $reference = $this->get('open_orchestra_model.repository.reference')->find($facade->id);
        if (!$reference instanceof ReferenceInterface) {
            throw new ReferenceNotFoundHttpException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $reference);
        $referenceSource = clone $reference;

        $this->get('open_orchestra_api.transformer_manager')->get('reference')->reverseTransform($facade, $reference);
        $status = $reference->getStatus();
        if ($status !== $referenceSource->getStatus()) {
            if (!$this->isGranted($status, $referenceSource)) {
                throw new StatusChangeNotGrantedHttpException();
            }

            $this->updateStatus($referenceSource, $reference, $saveOldPublishedVersion);
        }

        return array();
    }

    /**
     * @param string   $referenceId
     * @param string   $language
     * @param int|null $version
     *
     * @return null|ReferenceInterface
     */
    protected function findOneReference($referenceId, $language, $version = null)
    {
        $referenceRepository = $this->get('open_orchestra_model.repository.reference');
        $reference = $referenceRepository->findOneByLanguageAndVersion($referenceId, $language, $version);

        return $reference;
    }

    /**
     * @param ReferenceInterface $referenceSource
     * @param ReferenceInterface $reference
     * @param boolean          $saveOldPublishedVersion
     */
    protected function updateStatus(
        ReferenceInterface $referenceSource,
        ReferenceInterface $reference,
        $saveOldPublishedVersion
        ) {
            if (true === $reference->getStatus()->isPublishedState() && false === $saveOldPublishedVersion) {
                $oldPublishedVersion = $this->get('open_orchestra_model.repository.reference')->findOnePublished(
                    $reference->getReferenceId(),
                    $reference->getLanguage(),
                    $reference->getSiteId()
                    );
                if ($oldPublishedVersion instanceof ReferenceInterface) {
                    $this->get('object_manager')->remove($oldPublishedVersion);
                }
            }

            $this->get('object_manager')->flush();
            $event = new ReferenceEvent($reference, $referenceSource->getStatus());
            $this->dispatchEvent(ReferenceEvents::CONTENT_CHANGE_STATUS, $event);
    }

    /**
     * @param string               $language
     * @param ReferenceTypeInterface $referenceType
     *
     * @return array
     */
    protected function getMappingReferenceType($language, ReferenceTypeInterface $referenceType)
    {
        $mapping = array(
            'name' => 'name',
            'status_label' => 'status.labels.'.$language,
            'linked_to_site' => 'linkedToSite',
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
