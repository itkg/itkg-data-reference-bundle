<?php
namespace Itkg\ReferenceBundle\EventSubscriber;

use Itkg\ReferenceInterface\Repository\ReferenceTypeRepositoryInterface;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use Itkg\ReferenceApiBundle\Repository\ReferenceTypeRepository;
use Symfony\Component\Form\FormEvent;

/**
 * Class ReferenceTypeSubscriber
 */
class ReferenceTypeSubscriber extends AbstractBlockReferenceTypeSubscriber
{
    protected $translationChoiceManager;
    protected $referenceTypeRepository;
    protected $contentAttributClass;

    /**
     * @param ReferenceTypeRepositoryInterface $referenceTypeRepository
     * @param string                         $contentAttributClass
     * @param TranslationChoiceManager       $translationChoiceManager
     */
    public function __construct(
        ReferenceTypeRepositoryInterface $referenceTypeRepository,
        $contentAttributClass,
        TranslationChoiceManager $translationChoiceManager
    )
    {
        $this->referenceTypeRepository = $referenceTypeRepository;
        $this->contentAttributClass = $contentAttributClass;
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $referenceType = $this->referenceTypeRepository->findOneByReferenceTypeId($data->getReferenceType());

        if (is_object($referenceType)) {
            /** @var FieldTypeInterface $field */
            foreach ($referenceType->getFields() as $field) {
                $attribute = $data->getAttributeByName($field->getFieldId());
                $defaultValue = $field->getDefaultValue();
                if ($attribute) {
                    $defaultValue = $attribute->getValue();
                }
                $form->add($field->getFieldId(), $field->getType(), array_merge(
                    array(
                        'data' => $defaultValue,
                        'label' => $this->translationChoiceManager->choose($field->getLabels()),
                        'mapped' => false,
                    ),
                    $field->getFormOptions()
                ));
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $reference = $form->getData();
        $data = $event->getData();
        $referenceType = $this->referenceTypeRepository->findOneByReferenceTypeId($reference->getReferenceType());

        if (is_object($referenceType)) {
            foreach ($referenceType->getFields() as $field) {
                $fieldId = $field->getFieldId();
                if ($attribute = $reference->getAttributeByName($fieldId)) {
                    $attribute->setValue($this->transformData($data[$fieldId], $form->get($fieldId)));
                } elseif (is_null($attribute)) {
                    $contentAttributClass = $this->contentAttributClass;
                    $attribute = new $contentAttributClass;
                    $attribute->setName($fieldId);
                    $attribute->setValue($this->transformData($data[$fieldId], $form->get($fieldId)));
                    $reference->addAttribute($attribute);
                }
            }
        }
    }
}
