<?php

namespace Itkg\ReferenceBundle\Controller;

use Itkg\ReferenceInterface\Event\ReferenceEvent;
use Itkg\ReferenceInterface\Model\ReferenceInterface;
use Itkg\ReferenceInterface\ReferenceEvents;
use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ReferenceController
 */
class ReferenceController extends AbstractAdminController
{
    /**
     * Get Form Template related to content of $contentTypeId
     *
     * @param string $referenceTypeId
     *
     * @return string
     */
    protected function getFormTemplate($referenceTypeId)
    {
        $template = AbstractAdminController::TEMPLATE;

        $referenceType = $this->get('itkg_reference.repository.reference_type')
            ->findOneByReferenceTypeId($referenceTypeId);

        $customTemplate = $referenceType->getTemplate();

        if ($customTemplate != '' && $this->get('templating')->exists($customTemplate)) {
            $template = $customTemplate;
        }

        return $template;
    }

    /**
     * @param Request $request
     * @param string  $referenceType
     *
     * @Config\Route("/reference/new/{referenceType}", name="itkg_reference_bundle_reference_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REFERENCE_TYPE_FOR_REFERENCE')")
     *
     * @return Response
     */
    public function newAction(Request $request, $referenceType)
    {
        $referenceClass = $this->container->getParameter('itkg_reference.document.reference.class');
        /** @var ReferenceInterface $reference */
        $reference = new $referenceClass();
        $reference->setReferenceTypeId($referenceType);
        

        $form = $this->createForm('itkg_reference', $reference, array(
            'action' => $this->generateUrl('itkg_reference_bundle_reference_new', array(
                'referenceType' => $referenceType
            )),
            'method' => 'POST',
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($reference);
            $documentManager->flush();

            $this->dispatchEvent(ReferenceEvents::REFERENCE_CREATION, new ReferenceEvent($reference));

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('itkg_reference_bundle.form.reference.creation')
            );

            return $this->redirect(
                $this->generateUrl('itkg_reference_bundle_reference_form', array(
                    'referenceId' => $reference->getReferenceId()
                ))
            );
        }

        return $this->render(
            $this->getFormTemplate($referenceType),
            array('form' => $form->createView())
        );
    }

    /**
     * @param Request $request
     * @param         $referenceId
     *
     * @Config\Route("/reference/form/{referenceId}", name="itkg_reference_bundle_reference_form")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REFERENCE_TYPE_FOR_REFERENCE')")
     *
     * @return Response
     */
    public function formAction(Request $request, $referenceId)
    {
        $language = $request->get('language');

        $reference = $this->get('itkg_reference.repository.reference')
            ->findOneByIdAndLanguageNotDeleted($referenceId, $language);

        $form = $this->createForm('itkg_reference', $reference, array(
            'action' => $this->generateUrl('itkg_reference_bundle_reference_form', array(
                'referenceId' => $reference->getReferenceId(),
                'language' => $reference->getLanguage()
            ))
        ));

        $form->handleRequest($request);

        $this->handleForm(
            $form,
            $this->get('translator')->trans('itkg_reference_bundle.form.reference.success'),
            $reference
        );

        $this->dispatchEvent(ReferenceEvents::REFERENCE_UPDATE, new ReferenceEvent($reference));

        return $this->renderAdminForm(
            $form,
            array(),
            null,
            $this->getFormTemplate($reference->getReferenceTypeId())
        );
    }
}
