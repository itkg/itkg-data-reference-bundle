<?php

namespace Itkg\ReferenceApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class ReferenceTypeCollectionTransformer
 */
class ReferenceTypeCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $referenceTypeCollection
     *
     * @return FacadeInterface
     */
    public function transform($referenceTypeCollection)
    {
        $facade = $this->newFacade();

        foreach ($referenceTypeCollection as $referenceType) {
            $facade->addReferenceType($this->getTransformer('reference_type')->transform($referenceType));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null            $source
     *
     * @return ReferenceTypeInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        $referenceTypes = array();
        $referenceTypesFacade = $facade->getReferenceTypes();
        foreach ($referenceTypesFacade as $referenceTypeFacade) {
            $referenceType = $this->getTransformer('reference_type')->reverseTransform($referenceTypeFacade);
            if (null !== $referenceType) {
                $referenceTypes[] = $referenceType;
            }
        }

        return $referenceTypes;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reference_type_collection';
    }
}
