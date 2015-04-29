<?php
namespace Itkg\ReferenceBundle\Controller;

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
     *
     * @Config\Route("/reference-type/new", name="itkg_reference_bo_bundle_reference_type_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REFERENCE_TYPE')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $referenceTypeClass = $this->container->getParameter('itkg_reference.repository.reference_type.class');
        /** @var ReferenceTypeInterface $referenceType */
        $referenceType = new ReferenceType();

        $form = $this->createForm(
            'itkg_reference_type',
            $referenceType,
            array(
                'action' => $this->generateUrl('itkg_reference_bo_bundle_reference_type_new', array())
            )
        );

        $form->handleRequest($request);

        return $this->render('OpenOrchestraBackofficeBundle::form.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
