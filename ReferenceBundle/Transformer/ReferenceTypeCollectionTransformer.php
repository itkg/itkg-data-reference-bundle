<?php

namespace Itkg\ReferenceBundle\Transformer;

use Itkg\ReferenceBundle\Facade\ReferenceTypeCollectionFacade;
use OpenOrchestra\ApiBundle\Transformer\AbstractTransformer;

/**
 * Class ReferenceTypeCollectionTransformer
 */
class ReferenceTypeCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ReferenceInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new ReferenceTypeCollectionFacade();

        foreach ($mixed as $referenceType) {
            $facade->addReferenceType($this->getTransformer("reference_type")->transform($referenceType));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reference_type_collection';
    }
}
