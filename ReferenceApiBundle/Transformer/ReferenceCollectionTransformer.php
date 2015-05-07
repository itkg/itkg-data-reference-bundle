<?php

namespace Itkg\ReferenceApiBundle\Transformer;

use Itkg\ReferenceApiBundle\Facade\ReferenceCollectionFacade;
use OpenOrchestra\ApiBundle\Transformer\AbstractTransformer;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;

/**
 * Class ReferenceCollectionTransformer
 */
class ReferenceCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed, $referenceType = null)
    {
        $facade = new ReferenceCollectionFacade();

        foreach ($mixed as $reference) {
            $facade->addReference($this->getTransformer('reference')->transform($reference,$referenceType));
        }
        /*
        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_reference_list',
            array()
        ));*/

        if ($referenceType) {
            $facade->addLink('_self_add', $this->generateRoute(
                'itkg_reference_bundle_reference_new',
                array('referenceType' => $referenceType)
            ));
        }

        //$facade->addLink('_translate', $this->generateRoute('open_orchestra_api_translate'));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reference_collection';
    }
}
