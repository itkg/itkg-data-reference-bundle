<?php

namespace Itkg\ReferenceApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Transformer\AbstractTransformer;
use Itkg\ReferenceApiBundle\Facade\ReferenceFacade;
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

        $facade->addLink('_self_form', $this->generateRoute('itkg_reference_bundle_reference_form', array(//itkg_reference_bundle_reference_form
            'referenceId' => $mixed->getReferenceId(),
            'language' => $mixed->getLanguage()
        )));

        $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_content_delete', array(
            'contentId' => $mixed->getId()
        )));
/*
        $facade->addLink('_self', $this->generateRoute('open_orchestra_api_content_show', array(
            'contentId' => $mixed->getReferenceId(),
            'language' => $mixed->getLanguage()
        )));*/

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
