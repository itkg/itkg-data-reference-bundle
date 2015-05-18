<?php

namespace Itkg\ReferenceApiBundle\Transformer;

use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use Itkg\ReferenceApiBundle\Facade\ReferenceFacade;
use Itkg\ReferenceInterface\Model\ReferenceInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

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
        $facade->referenceType = $mixed->getReferenceTypeId();
        $facade->name = $mixed->getName();
        $facade->language = $mixed->getLanguage();
        $facade->createdAt = $mixed->getCreatedAt();
        $facade->updatedAt = $mixed->getUpdatedAt();
        $facade->deleted = $mixed->getDeleted();

        foreach ($mixed->getAttributes() as $attribute) {
            $facade->addAttribute($this->getTransformer('content_attribute')->transform($attribute));
        }

        $facade->addLink('_self_form', $this->generateRoute('itkg_reference_bundle_reference_form', array(
            'referenceId' => $mixed->getReferenceId(),
            'language' => $mixed->getLanguage()
        )));

        $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_reference_delete', array(
            'referenceId' => $mixed->getReferenceId()
        )));

        $facade->addLink('_self_without_parameters', $this->generateRoute('open_orchestra_api_reference_show', array(
            'referenceId' => $mixed->getReferenceId()
        )));

        $facade->addLink('_language_list', $this->generateRoute('open_orchestra_api_parameter_languages_show'));

        $facade->addLink('_translate', $this->generateRoute('open_orchestra_api_translate'));

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
