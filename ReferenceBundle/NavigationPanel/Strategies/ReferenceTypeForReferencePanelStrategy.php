<?php
namespace Itkg\ReferenceBundle\NavigationPanel\Strategies;

use Itkg\ReferenceInterface\Repository\ReferenceTypeRepositoryInterface;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AbstractNavigationPanelStrategy;

/**
 * Class ReferenceTypeForReferencePanelStrategy
 */

class ReferenceTypeForReferencePanelStrategy extends AbstractNavigationPanelStrategy
{
    const ROLE_ACCESS_REFERENCE_TYPE_FOR_REFERENCE = 'ROLE_ACCESS_REFERENCE_TYPE_FOR_REFERENCE';

    /**
     * @var referenceTypeRepositoryInterface
     */
    protected $referenceTypeRepository;

    /**
     * @param ReferenceTypeRepositoryInterface $referenceTypeRepository
     */
    public function __construct(ReferenceTypeRepositoryInterface $referenceTypeRepository)
    {
        $this->referenceTypeRepository = $referenceTypeRepository;
    }

    /**
     * @return string
     */
    public function show()
    {
        $referenceTypes = $this->referenceTypeRepository->findAllByNotDeleted();

        return $this->render(
            'ItkgReferenceBundle:EditorialPanel:showReferenceTypeForReference.html.twig',
            array(
                'referenceTypes' => $referenceTypes
            )
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'editorial';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reference_type_for_reference';
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return self::ROLE_ACCESS_REFERENCE_TYPE_FOR_REFERENCE;
    }
}
