<?php

namespace Itkg\ReferenceApiBundle\Transformer;

use Itkg\ReferenceApiBundle\Facade\ReferenceCollectionFacade;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class ReferenceCollectionTransformer
 */
class ReferenceCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     * @param string|null     $referenceType
     *
     * @return FacadeInterface
     */
    public function transform($mixed, $referenceType = null)
    {
        $facade = new ReferenceCollectionFacade();

        foreach ($mixed as $reference) {
            $facade->addReference($this->getTransformer('reference')->transform($reference, $referenceType));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null            $source
     *
     * @return array
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        $references = array();
        $referencesFacade = $facade->getReferences();
        foreach ($referencesFacade as $referenceFacade) {
            $reference = $this->getTransformer('reference')->reverseTransform($referenceFacade);
            if (null !== $reference) {
                $references[] = $reference;
            }
        }

        return $references;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reference_collection';
    }
}
