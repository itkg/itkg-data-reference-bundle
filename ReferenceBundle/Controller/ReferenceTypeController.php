<?php
namespace Itkg\ReferenceBundle\Controller;

use Itkg\ReferenceInterface\ReferenceTypeEvents;
use Itkg\ReferenceInterface\Event\ReferenceTypeEvent;
use Itkg\ReferenceBundle\Document\ReferenceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class ReferenceTypeController
 *
 * @Config\Route("reference-type")
 */
class ReferenceTypeController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $referenceTypeId
     *
     * @Config\Route("/reference-type/form/{referenceTypeId}", name="itkg_reference_bundle_reference_type_form")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REFERENCE_TYPE')")
     *
     * @return Response
     */
    public function formAction(Request $request, $referenceTypeId)
    {
        $referenceType = $this->get('itkg_reference.repository.reference_type')->findOneByReferenceTypeId($referenceTypeId);

        $newReferenceType = $this->get('itkg_reference.manager.reference_type')->duplicate($referenceType);
foreach ($referenceType->getFields() as $field)
{
	echo "<hr>";	var_dump($field->getLabels());
}
//var_dump(count($newReferenceType->getFields()));
$form = $this->createForm(
            'itkg_reference_type',
            $newReferenceType,
            array(
                'action' => $this->generateUrl('itkg_reference_bundle_reference_type_form', array(
                        'referenceTypeId' => $referenceTypeId,
                    )),
                'method' => 'POST'
            )
        );

        $form->handleRequest($request);
        if (!$request->get('no_save')) {
            $this->handleForm($form, $this->get('translator')->trans('open_orchestra_backoffice.form.content_type.success'), $newReferenceType);
            $this->dispatchEvent(ReferenceTypeEvents::REFERENCE_TYPE_UPDATE, new ReferenceTypeEvent($newReferenceType));
        }

        return $this->render('OpenOrchestraBackofficeBundle::form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/reference-type/new", name="itkg_reference_bundle_reference_type_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REFERENCE_TYPE')")
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
                'action' => $this->generateUrl('itkg_reference_bundle_reference_type_new', array())
            )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($referenceType);
            $documentManager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('open_orchestra_backoffice.form.content_type.creation')
            );

            $this->dispatchEvent(ReferenceTypeEvents::REFERENCE_TYPE_CREATE, new ReferenceTypeEvent($referenceType));

            return $this->redirect(
                $this->generateUrl('itkg_reference_bundle_reference_type_new', array('referenceTypeId' => $referenceType->getReferenceTypeId()))
            );
        }

        return $this->render('OpenOrchestraBackofficeBundle::form.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
