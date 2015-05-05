<?php

namespace Itkg\ReferenceApiBundle\Transformer;

use Itkg\ReferenceApiBundle\Document\ReferenceType;
use Itkg\ReferenceApiBundle\Facade\ReferenceTypeFacade;
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

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_reference_type_show',
            array('referenceTypeId' => $mixed->getReferenceTypeId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_reference_type_delete', // TODO
            array('referenceTypeId' => $mixed->getReferenceTypeId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'itkg_reference_bundle_reference_type_form', // TODO
            array('referenceTypeId' => $mixed->getReferenceTypeId())
        ));

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
