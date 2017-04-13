<?php

namespace Itkg\ReferenceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Itkg\ReferenceBundle\EventSubscriber\ReferenceTypeTypeSubscriber;

/**
 * Class ReferenceTypeType
 */
class ReferenceTypeType extends AbstractType
{
    protected $referenceTypeClass;
    protected $backOfficeLanguages;
    protected $translator;
    protected $referenceTypeTypeSubscriber;

    /**
     * @param string                      $referenceTypeClass
     * @param TranslatorInterface         $translator
     * @param array                       $backOfficeLanguages
     * @param ReferenceTypeTypeSubscriber $referenceTypeTypeSubscriber,
     */
    public function __construct(
        $referenceTypeClass,
        TranslatorInterface $translator,
        array $backOfficeLanguages,
        ReferenceTypeTypeSubscriber $referenceTypeTypeSubscriber
    ) {
        $this->referenceTypeClass = $referenceTypeClass;
        $this->translator = $translator;
        $this->backOfficeLanguages = $backOfficeLanguages;
        $this->referenceTypeTypeSubscriber = $referenceTypeTypeSubscriber;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('names', 'oo_multi_languages', array(
                'label' => 'itkg_reference.form.reference_type.names',
                'languages' => $this->backOfficeLanguages,
                'group_id' => 'property',
                'sub_group_id' => 'property',
            ))
            ->add('referenceTypeId', 'text', array(
                'label' => 'itkg_reference.form.reference_type.reference_type_id',
                'group_id' => 'property',
                'sub_group_id' => 'property',
                'attr' => array(
                    'class' => 'generate-id-dest'
                )
            ))
            ->add('template', 'text', array(
                'label' => 'itkg_reference.form.reference_type.template.label',
                'required' => false,
                'attr' => array('help_text' => 'itkg_reference.form.reference_type.template.helper'),
                'group_id' => 'property',
                'sub_group_id' => 'customization',
            ))
            ->add('defaultListable', 'collection', array(
                'required' => false,
                'type' => 'oo_default_listable_checkbox',
                'label' => false,
                'group_id' => 'property',
                'sub_group_id' => 'visible',
            ))
            ->add('fields', 'collection', array(
                'type' => 'oo_field_type',
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'sortable' => true,
                'attr' => array(
                    'data-prototype-label-add' => $this->translator->trans('open_orchestra_backoffice.form.field_type.add'),
                    'data-prototype-label-new' => $this->translator->trans('open_orchestra_backoffice.form.field_type.new'),
                    'data-prototype-label-remove' => $this->translator->trans('open_orchestra_backoffice.form.field_type.delete'),
                ),
                'options' => array( 'label' => false ),
                'group_id' => 'field',
                'required' => false
            ));

        $builder->addEventSubscriber($this->referenceTypeTypeSubscriber);
        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->referenceTypeClass,
                'delete_button' => false,
                'new_button' => false,
                'group_enabled' => true,
                'group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'itkg_reference.form.reference_type.group.property',
                    ),
                    'field' => array(
                        'rank' => 1,
                        'label' => 'itkg_reference.form.reference_type.group.field',
                    ),
                ),
                'sub_group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'itkg_reference.form.reference_type.sub_group.property',
                    ),
                    'customization' => array(
                        'rank' => 1,
                        'label' => 'itkg_reference.form.reference_type.sub_group.customization',
                    ),
                    'share' => array(
                        'rank' => 2,
                        'label' => 'itkg_reference.form.reference_type.sub_group.share',
                    ),
                    'visible' => array(
                        'rank' => 3,
                        'label' => 'itkg_reference.form.reference_type.sub_group.visible',
                    ),
                    'version' => array(
                        'rank' => 4,
                        'label' => 'itkg_reference.form.reference_type.sub_group.version',
                    ),
                ),
            )
        );
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['delete_button'] = $options['delete_button'];
        $view->vars['new_button'] = $options['new_button'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'itkg_reference_type';
    }
}
