<?php

namespace Itkg\ReferenceBundle\Form\Type;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use OpenOrchestra\BackofficeBundle\EventListener\TranslateValueInitializerListener;
use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;

/**
 * Class ReferenceType
 */

class ReferenceTypeType extends AbstractType
{
    protected $translateValueInitializer;
    protected $referenceTypeClass;
    protected $translator;

    /**
     * @param string                            $referenceTypeClass
     * @param TranslatorInterface               $translator
     * @param TranslateValueInitializerListener $translateValueInitializer
     */

    public function __construct(
        $referenceTypeClass,
        TranslatorInterface $translator,
        TranslateValueInitializerListener $translateValueInitializer
    )
    {
        $this->translateValueInitializer = $translateValueInitializer;
        $this->referenceTypeClass = $referenceTypeClass;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this->translateValueInitializer, 'preSetData'));

        $builder
            ->add('referenceTypeId', 'text', array(
                'label' => 'itkg_reference_bundle.backoffice.form.reference_type.referenceTypeId',
                'attr' => array(
                    'class' => 'generate-id-dest'
                )
            ))
            ->add('names', 'translated_value_collection', array(
                'label' => 'itkg_reference_bundle.backoffice.form.reference_type.names'
            ))
            ->add('fields', 'collection', array(
                'type' => 'field_type',
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'itkg_reference_bundle.backoffice.form.reference_type.fields',
                'attr' => array(
                    'data-prototype-label-add' => $this->translator->trans('open_orchestra_backoffice.form.field_type.add'),
                    'data-prototype-label-new' => $this->translator->trans('open_orchestra_backoffice.form.field_type.new'),
                    'data-prototype-label-remove' => $this->translator->trans('open_orchestra_backoffice.form.field_type.delete'),
                )
            ));

        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'itkg_reference_type';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->referenceTypeClass,
        ));
    }
}
