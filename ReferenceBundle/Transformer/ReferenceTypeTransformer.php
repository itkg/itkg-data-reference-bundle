<?php

namespace Itkg\ReferenceBundle\Transformer;

use Itkg\ReferenceBundle\Document\ReferenceType;

use Itkg\ReferenceBundle\Facade\ReferenceTypeFacade;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ApiBundle\Transformer\AbstractTransformer;


/**
 * Class ReferenceTypeTransformer
 */
class ReferenceTypeTransformer extends AbstractTransformer
{
    protected $translationChoiceManager;

    /**
     * @param TranslationChoiceManager $translationChoiceManager
     */
    public function __construct(TranslationChoiceManager $translationChoiceManager)
    {
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new ReferenceTypeFacade();

        $facade->name = $this->translationChoiceManager->choose($mixed->getNames());
        $facade->referenceTypeId = $mixed->getReferenceTypeId();

        foreach ($mixed->getFields() as $field) {
            $facade->addField($this->getTransformer('field_type')->transform($field));
        }
/*
        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_content_type_show',//'open_orchestra_api_reference_type_show',
            array('contentTypeId' => $mixed->getContentTypeId())//array('referenceTypeId' => $mixed->getReferenceTypeId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_content_type_delete',
            array('contentTypeId' => $mixed->getContentTypeId())//array('contentTypeId' => $mixed->getReferenceTypeId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_content_type_form',//'open_orchestra_backoffice_reference_type_form',
            array('contentTypeId' => $mixed->getContentTypeId())//array('referenceTypeId' => $mixed->getReferenceTypeId())
        ));
*/
        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reference_type';
    }
}
