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
