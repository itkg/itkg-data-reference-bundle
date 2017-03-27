<?php

namespace Itkg\ReferenceBundle\Controller;

use Itkg\ReferenceInterface\Event\ReferenceEvent;
use Itkg\ReferenceInterface\Model\ReferenceInterface;
use Itkg\ReferenceInterface\ReferenceEvents;
use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Itkg\ReferenceInterface\Model\ReferenceTypeInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class ReferenceController
 */
class ReferenceController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $referenceId
     * @param string  $language
     * @param string  $version
     *
     * @Config\Route(
     *     "/reference/form/{referenceId}/{language}/{version}",
     *      name="itkg_reference_bundle_reference_form",
     *      defaults={"version": null},
     * )
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $referenceId, $language, $version)
    {
        $reference = $this->get('itkg_reference.repository.reference')->findOneByLanguageAndVersion($referenceId, $language, $version);
        if (!$reference instanceof ReferenceInterface) {
            throw new \UnexpectedValueException();
        }
        $referenceType = $this->get('itkg_reference.repository.reference_type')->findOneByReferenceTypeIdInLastVersion($reference->getReferenceType());
        if (!$referenceType instanceof ReferenceTypeInterface) {
            throw new \UnexpectedValueException();
        }
//         $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $reference);

        $publishedReferences = $this->get('itkg_reference.repository.reference')->findAllPublishedByReferenceId($referenceId);
        $isUsed = false;
        foreach ($publishedReferences as $publishedReference) {
            $isUsed = $isUsed || $publishedReference->isUsed();
        }
        $options = array(
            'action' => $this->generateUrl('itkg_reference_bundle_reference_form', array(
                'referenceId' => $reference->getReferenceId(),
                'language' => $reference->getLanguage(),
                'version' => $reference->getVersion(),
            )),
            'delete_button' => $this->canDeleteReference($reference),
            'need_link_to_site_defintion' => false,
            'is_blocked_edition' => $reference->getStatus() ? $reference->getStatus()->isBlockedEdition() : false,
        );
        $form = $this->createForm('itkg_reference', $reference, $options);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('object_manager')->flush();
            $this->dispatchEvent(ReferenceEvents::REFERENCE_UPDATE, new ReferenceEvent($reference));

            $message =  $this->get('translator')->trans('open_orchestra_backoffice.form.reference.success');
            $this->get('session')->getFlashBag()->add('success', $message);

        }

        return $this->renderAdminForm(
            $form,
            array(),
            null,
            $this->getFormTemplate($reference->getReferenceType()
        ));
    }

    /**
     * @param Request $request
     * @param string  $referenceTypeId
     * @param string  $language
     *
     * @Config\Route("/reference/new/{referenceTypeId}/{language}", name="itkg_reference_bundle_reference_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request, $referenceTypeId, $language)
    {
        $referenceManager = $this->get('open_orchestra_backoffice.manager.reference');
        $referenceType = $this->get('itkg_reference.repository.reference_type')->findOneByReferenceTypeIdInLastVersion($referenceTypeId);
        if (!$referenceType instanceof ReferenceTypeInterface) {
            throw new \UnexpectedValueException();
        }
        $reference = $referenceManager->initializeNewReference($referenceTypeId, $language, $referenceType->isLinkedToSite() && $referenceType->isAlwaysShared());
        if (!$reference instanceof ReferenceInterface) {
            throw new \UnexpectedValueException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $reference);

        $form = $this->createForm('itkg_reference', $reference, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_reference_new', array(
                'referenceTypeId' => $referenceTypeId,
                'language' => $language,
            )),
            'method' => 'POST',
            'new_button' => true,
            'need_link_to_site_defintion' => $referenceType->isLinkedToSite() && !$referenceType->isAlwaysShared(),
            'is_blocked_edition' => $reference->getStatus() ? $reference->getStatus()->isBlockedEdition() : false,
        ));

        $status = $reference->getStatus();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $reference = $referenceManager->setVersionName($reference);
            $documentManager = $this->get('object_manager');
            $documentManager->persist($reference);
            $this->dispatchEvent(ReferenceEvents::REFERENCE_CREATION, new ReferenceEvent($reference));

            $this->createReferenceInNewLanguage($reference, $language);

            $documentManager->flush();
            if ($status->getId() !== $reference->getStatus()->getId()) {
                $this->dispatchEvent(ReferenceEvents::REFERENCE_CHANGE_STATUS, new ReferenceEvent($reference, $status));
            }

            $message = $this->get('translator')->trans('open_orchestra_backoffice.form.reference.creation');
            $response = new Response(
                $message,
                Response::HTTP_CREATED,
                array('Reference-type' => 'text/plain; charset=utf-8', 'referenceId' => $reference->getReferenceId(), 'version' => $reference->getVersion())
            );

            return $response;
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param ReferenceInterface $reference
     *
     * @return bool
     */
    protected function canDeleteReference(ReferenceInterface $reference) {
        $referenceRepository = $this->get('itkg_reference.repository.reference');

        return false === $referenceRepository->hasReferenceIdWithoutAutoUnpublishToState($reference->getReferenceId()) &&
               $this->isGranted(ContributionActionInterface::DELETE, $reference);
    }

    /**
     * Get Form Template related to reference of $referenceTypeId
     *
     * @param string $referenceTypeId
     *fof
     * @return string
     */
    protected function getFormTemplate($referenceTypeId)
    {
        $template = AbstractAdminController::TEMPLATE;

        $referenceType = $this->get('itkg_reference.repository.reference_type')->findOneByReferenceTypeIdInLastVersion($referenceTypeId);

        if ($referenceType instanceof ReferenceTypeInterface) {
            $customTemplate = $referenceType->getTemplate();

            if ($customTemplate != '' && $this->get('templating')->exists($customTemplate)) {
                $template = $customTemplate;
            }
        }

        return $template;
    }

    /**
     * @param ReferenceInterface $reference
     * @param string           $currentLanguage
     */
    protected function createReferenceInNewLanguage(ReferenceInterface $reference, $currentLanguage)
    {
        $languages = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteLanguages();
        foreach ($languages as $siteLanguage) {
            if ($currentLanguage !== $siteLanguage) {
                $translatedReference = $this->get('open_orchestra_backoffice.manager.reference')->createNewLanguageReference($reference, $siteLanguage);
                $this->get('object_manager')->persist($translatedReference);
                $this->dispatchEvent(ReferenceEvents::REFERENCE_CREATION, new ReferenceEvent($translatedReference));
            }
        }
    }
}
