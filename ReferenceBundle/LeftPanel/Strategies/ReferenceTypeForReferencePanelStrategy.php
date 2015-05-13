<?php
namespace Itkg\ReferenceBundle\LeftPanel\Strategies;

use Itkg\ReferenceInterface\Repository\ReferenceTypeRepositoryInterface;
use OpenOrchestra\Backoffice\LeftPanel\Strategies\AbstractLeftPanelStrategy;

/**
 * Class ReferenceTypeForReferencePanelStrategy
 */

class ReferenceTypeForReferencePanelStrategy extends AbstractLeftPanelStrategy
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
        return self::EDITORIAL;
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
