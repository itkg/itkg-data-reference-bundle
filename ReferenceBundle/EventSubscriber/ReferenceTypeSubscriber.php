<?php

namespace Itkg\ReferenceBundle\EventSubscriber;

use Itkg\ReferenceInterface\Repository\ReferenceTypeRepositoryInterface;
use OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerManager;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use Itkg\ReferenceApiBundle\Repository\ReferenceTypeRepository;
use Symfony\Component\Form\FormEvent;
use Itkg\ReferenceInterface\Model\ReferenceTypeInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormEvents;

/**
 * Class ReferenceTypeSubscriber
 */
class ReferenceTypeSubscriber implements EventSubscriberInterface
{
    protected $multiLanguagesChoiceManager;
    protected $referenceTypeRepository;
    protected $referenceAttributeClass;
    protected $fieldTypesConfiguration;
    protected $valueTransformerManager;
    protected $translator;

    /**
     * @param ReferenceTypeRepositoryInterface     $referenceTypeRepository
     * @param string                               $referenceAttributeClass
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param array                                $fieldTypesConfiguration
     * @param ValueTransformerManager              $valueTransformerManager
     * @param TranslatorInterface                  $translator
     */
    public function __construct(
        ReferenceTypeRepositoryInterface $referenceTypeRepository,
        $referenceAttributeClass,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        $fieldTypesConfiguration,
        ValueTransformerManager $valueTransformerManager,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->referenceTypeRepository = $referenceTypeRepository;
        $this->referenceAttributeClass = $referenceAttributeClass;
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->fieldTypesConfiguration = $fieldTypesConfiguration;
        $this->valueTransformerManager = $valueTransformerManager;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::POST_SUBMIT => 'postSubmit',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $reference = $event->getData();
        $referenceType = $this->referenceTypeRepository->findOneByReferenceTypeId($reference->getReferenceType());
        if ($referenceType instanceof ReferenceTypeInterface) {
            $this->addReferenceTypeFieldsToForm($referenceType->getFields(), $form, false);
        }
    }

    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $referenceType = $this->referenceTypeRepository->findOneByReferenceTypeId($data->getReferenceType());
        if ($referenceType instanceof ReferenceTypeInterface) {
            foreach ($referenceType->getFields() as $referenceTypeField) {
                $referenceTypeFieldId = $referenceTypeField->getFieldId();
                $dataAttribute = $data->getAttributeByName($referenceTypeFieldId);
                $fieldValue = ($dataAttribute) ? $dataAttribute->getValue() : $referenceTypeField->getDefaultValue();
                try {
                    $form->get($referenceTypeFieldId)->setData($fieldValue);
                } catch (TransformationFailedException $e) {
                    $message = $this->translator->trans("open_orchestra_backoffice.form.reference.transformation_error");
                    $error = new FormError($message);
                    $form->get($referenceTypeFieldId)->addError($error);
                }
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $reference = $form->getData();
        $referenceType = $this->referenceTypeRepository->findOneByReferenceTypeId($reference->getReferenceType());

        if ($referenceType instanceof ReferenceTypeInterface) {
            foreach ($referenceType->getFields() as $referenceTypeField) {
                $referenceTypeFieldId = $referenceTypeField->getFieldId();
                $value = $form->get($referenceTypeFieldId)->getData();
                $attribute = $reference->getAttributeByName($referenceTypeFieldId);
                if (is_null($attribute)) {
                    /** @var ReferenceAttributeInterface $attribute */
                    $attribute = new $this->referenceAttributeClass();
                    $attribute->setName($referenceTypeFieldId);
                    $reference->addAttribute($attribute);
                }

                $attribute->setValue($value);
                $attribute->setType($referenceTypeField->getType());
                $attribute->setStringValue($this->valueTransformerManager->transform($attribute->getType(), $value));
            }
        }
    }

    /**
     * Add $referenceTypeFields to $form with values in $data if reference type is still valid
     *
     * @param array<FieldTypeInterface> $referenceTypeFields
     * @param FormInterface             $form
     */
    protected function addReferenceTypeFieldsToForm($referenceTypeFields, FormInterface $form)
    {
        /** @var FieldTypeInterface $referenceTypeField */
        foreach ($referenceTypeFields as $referenceTypeField) {

            if (isset($this->fieldTypesConfiguration[$referenceTypeField->getType()])) {
                $this->addFieldToForm($referenceTypeField, $form);
            }
        }
    }

    /**
     * Add $referenceTypeField to $form with value $fieldValue
     *
     * @param FieldTypeInterface $referenceTypeField
     * @param FormInterface      $form
     */
    protected function addFieldToForm(FieldTypeInterface $referenceTypeField, FormInterface $form)
    {
        $fieldTypeConfiguration = $this->fieldTypesConfiguration[$referenceTypeField->getType()];

        $fieldParameters = array_merge(
            array(
                'label' => $this->multiLanguagesChoiceManager->choose($referenceTypeField->getLabels()),
                'mapped' => false,
                'group_id' => 'data',
                'sub_group_id' => 'data',
            ),
            $this->getFieldOptions($referenceTypeField)
        );

        if (isset($fieldParameters['required']) && $fieldParameters['required'] === true) {
            $fieldParameters['constraints'] = new NotBlank();
        }
        $form->add(
            $referenceTypeField->getFieldId(),
            $fieldTypeConfiguration['type'],
            $fieldParameters
        );
    }

    /**
     * Get $referenceTypeField options from conf and complete it with $referenceTypeField setted values
     *
     * @param FieldTypeInterface $referenceTypeField
     *
     * @return array
     */
    protected function getFieldOptions(FieldTypeInterface $referenceTypeField)
    {
        $referenceTypeOptions = $referenceTypeField->getFormOptions();
        $options = array();
        $field = $this->fieldTypesConfiguration[$referenceTypeField->getType()];
        if (isset($field['options'])) {
            $configuratedOptions = $field['options'];
            foreach ($configuratedOptions as $optionName => $optionConfiguration) {
                $options[$optionName] = (isset($referenceTypeOptions[$optionName])) ? $referenceTypeOptions[$optionName] : $optionConfiguration['default_value'];
            }
        }
        return $options;
    }
}
