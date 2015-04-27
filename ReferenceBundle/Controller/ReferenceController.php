<?php

namespace Itkg\ReferenceBundle\Controller;

use Doctrine\Common\Persistence\Event\ManagerEventArgs;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\ApiBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use OpenOrchestra\ApiBundle\Controller\Annotation as Api;

/**
 * Class ReferenceController
 *
 * @Route("reference")
 */
class ReferenceController extends BaseController
{
    /**
     * @param string  $referenceId
     *
     * @Route("/reference/{referenceId}", name="open_orchestra_api_itkg_reference_getId")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function getIdAction($referenceId)
    {
        $reference = $this->get("itkg_reference.repository.reference")->findOneByIdAndLanguageNotDeleted($referenceId, "FR");

        $facadeReference = $this->get('open_orchestra_api.transformer_manager')->get('reference')->transform($reference);

        return $facadeReference;
    }
}
