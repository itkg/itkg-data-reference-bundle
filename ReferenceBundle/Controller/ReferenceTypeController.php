<?php

namespace Itkg\ReferenceBundle\Controller;

use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\ModelInterface\ContentTypeEvents;
use OpenOrchestra\ModelInterface\Event\ContentTypeEvent;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\ApiBundle\Controller\Annotation as Api;

/**
 * Class ContentTypeController
 *
 * @Config\Route("reference-type")
 */
class ReferenceTypeController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $referenceTypeId
     *
     * @Config\Route("/form/{referenceTypeId}", name="open_orchestra_api_reference_type_form")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REFERENCE_TYPE')")
     *
     * @return Response
     */
    public function formAction(Request $request, $referenceTypeId)
    {
        $form = $this->createForm(
            'reference_type',
            $newContentType,
            array(
                'action' => $this->generateUrl('open_orchestra_backoffice_reference_type_form', array(
                        'contentTypeId' => $referenceTypeId,
                    )),
                'method' => 'POST'
            )
        );

        return $this->render('OpenOrchestraBackofficeBundle::form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Config\Route("", name="open_orchestra_api_reference_type_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REFERENCE_TYPE')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $referenceTypeCollection = $this->get('itkg_reference.repository.reference_type')->findAllByDeleted();

        return $this->get('open_orchestra_api.transformer_manager')->get('reference_type_collection')->transform($referenceTypeCollection);
    }
}