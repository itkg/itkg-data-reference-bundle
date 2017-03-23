<?php

namespace Itkg\ReferenceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;

/**
 * Class ReferenceTypeController
 */
class ReferenceTypeController  extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $referenceTypeId
     *
     * @Config\Route("/reference-type/form/{referenceTypeId}", name="open_orchestra_backoffice_reference_type_form")
     * @Config\Method({"GET", "POST", "PATCH"})
     *
     * @return Response
     */
    public function formAction(Request $request, $referenceTypeId)
    {
        $referenceType = $this->get('open_orchestra_model.repository.reference_type')->findOneByReferenceTypeIdInLastVersion($referenceTypeId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $referenceType);

        $newReferenceType = $this->get('open_orchestra_backoffice.manager.reference_type')->duplicate($referenceType);
        $action = $this->generateUrl('open_orchestra_backoffice_reference_type_form', array('referenceTypeId' => $referenceTypeId));
        $form = $this->createReferenceTypeForm($request, array(
            'action' => $action,
            'delete_button' => ($this->isGranted(ContributionActionInterface::DELETE, $newReferenceType) && 0 == $this->get('open_orchestra_model.repository.reference')->countByReferenceType($referenceTypeId)),
            'need_link_to_site_defintion' => false,
        ), $newReferenceType);

        $form->handleRequest($request);
        if ('PATCH' !== $request->getMethod()) {
            if ($this->handleForm($form, $this->get('translator')->trans('open_orchestra_backoffice.form.reference_type.success'), $newReferenceType)) {
                $this->dispatchEvent(ReferenceTypeEvents::CONTENT_TYPE_UPDATE, new ReferenceTypeEvent($newReferenceType));
            }
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/reference-type/new", name="open_orchestra_backoffice_reference_type_new")
     * @Config\Method({"GET", "POST", "PATCH"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        /** @var ReferenceTypeInterface $referenceType */
        $referenceType = $this->get('open_orchestra_backoffice.manager.reference_type')->initializeNewReferenceType();

        $action = $this->generateUrl('open_orchestra_backoffice_reference_type_new', array());
        $form = $this->createReferenceTypeForm($request, array(
            'action' => $action,
            'new_button' => true,
            'need_link_to_site_defintion' => true,
        ), $referenceType);

        $form->handleRequest($request);
        if ('PATCH' !== $request->getMethod()) {
            if ($form->isValid()) {
                $language = $this->get('open_orchestra_backoffice.context_manager')->getCurrentLocale();
                $documentManager = $this->get('object_manager');
                $documentManager->persist($referenceType);
                $documentManager->flush();
                $message = $this->get('translator')->trans('open_orchestra_backoffice.form.reference_type.creation');
                $this->dispatchEvent(ReferenceTypeEvents::CONTENT_TYPE_CREATE, new ReferenceTypeEvent($referenceType));
                $response = new Response(
                    $message,
                    Response::HTTP_CREATED,
                    array('Reference-type' => 'text/plain; charset=utf-8', 'referenceTypeId' => $referenceType->getReferenceTypeId(), 'name' => $referenceType->getName($language))
                );

                return $response;
            }
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request              $request
     * @param string               $option
     * @param ReferenceTypeInterface $referenceType
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createReferenceTypeForm(Request $request, $option, ReferenceTypeInterface $referenceType)
    {
        $method = "POST";
        if ("PATCH" === $request->getMethod()) {
            $option["validation_groups"] = false;
            $method = "PATCH";
        }
        $option["method"] = $method;

        return $this->createForm('oo_reference_type', $referenceType, $option);
    }
}
