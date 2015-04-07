<?php

namespace Itkg\ReferenceBundle\Transformer;

use OpenOrchestra\ApiBundle\Transformer\AbstractTransformer;
use Itkg\ReferenceBundle\Facade\ReferenceFacade;
use Itkg\ReferenceInterface\Model\ReferenceInterface;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ReferenceTransformer
 */
class ReferenceTransformer extends AbstractTransformer
{
    /**
     * @param ReferenceInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new ReferenceFacade();

        $facade->referenceId = $mixed->getReferenceId();
        $facade->id = $mixed->getId();
        $facade->referenceType = $mixed->getReferenceType();
        $facade->name = $mixed->getName();
        $facade->language = $mixed->getLanguage();
        $facade->createdAt = $mixed->getCreatedAt();
        $facade->updatedAt = $mixed->getUpdatedAt();
        $facade->deleted = $mixed->getDeleted();

        foreach ($mixed->getAttributes() as $attribute) {
            $facade->addAttribute($this->getTransformer('content_attribute')->transform($attribute));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reference';
    }
}
