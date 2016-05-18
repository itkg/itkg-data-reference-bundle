<?php

namespace Itkg\ReferenceBundle\Controller;

use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use Itkg\ReferenceInterface\ReferenceTypeEvents;
use Itkg\ReferenceInterface\Event\ReferenceTypeEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class ReferenceTypeController
 */
class ReferenceTypeController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $referenceTypeId
     *
     * @Config\Route("/reference-type/form/{referenceTypeId}", name="itkg_reference_bundle_reference_type_form")
     * @Config\Method({"GET", "POST", "PATCH"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_REFERENCE_TYPE')")
     *
     * @return Response
     */
    public function formAction(Request $request, $referenceTypeId)
    {
        $referenceType = $this->get('itkg_reference.repository.reference_type')->findOneByReferenceTypeId($referenceTypeId);

        $form = $this->createForm(
            'itkg_reference_type',
            $referenceType,
            array(
                'action' => $this->generateUrl('itkg_reference_bundle_reference_type_form', array(
                        'referenceTypeId' => $referenceTypeId,
                    )),
                'method' => $this->getMethod($request),
            )
        );

        $form->handleRequest($request);
        if (!$request->get('no_save')) {
            $this->handleForm($form, $this->get('translator')->trans('itkg_reference_bundle.form.reference_type.success'), $referenceType);
            $this->dispatchEvent(ReferenceTypeEvents::REFERENCE_TYPE_UPDATE, new ReferenceTypeEvent($referenceType));
        }

        return $this->render('OpenOrchestraBackofficeBundle::form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/reference-type/new", name="itkg_reference_bundle_reference_type_new")
     * @Config\Method({"GET", "POST", "PATCH"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_REFERENCE_TYPE')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $referenceTypeClass = $this->container->getParameter('itkg_reference.document.reference_type.class');

        /** @var ReferenceTypeInterface $referenceType */
        $referenceType = new $referenceTypeClass();

        $form = $this->createForm(
            'itkg_reference_type',
            $referenceType,
            array(
                'action' => $this->generateUrl('itkg_reference_bundle_reference_type_new', array()),
                'method' => $this->getMethod($request),
            )
        );

        $form->handleRequest($request);
        if (!$request->get('no_save')) {
            $handleForm = $this->handleForm($form, $this->get('translator')->trans('itkg_reference_bundle.form.reference_type.creation'), $referenceType);

            if ( $handleForm && !is_null($referenceType->getId())) {
                $this->dispatchEvent(ReferenceTypeEvents::REFERENCE_TYPE_CREATE, new ReferenceTypeEvent($referenceType));

                return $this->redirect($this->generateUrl('itkg_reference_bundle_reference_type_form', array(
                    'referenceTypeId' => $referenceType->getReferenceTypeId()
                )));
            }
        }

        return $this->render('OpenOrchestraBackofficeBundle::form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function getMethod(Request $request){

        return $request->get('no_save') ? 'PATCH' : 'POST';
    }
}
