<?php

namespace Itkg\ReferenceBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Itkg\ReferenceBundle\ReferenceFormEvents;
use Itkg\ReferenceBundle\Event\ReferenceFormEvent;

/**
 * Class ReferenceType
 */
class ReferenceType extends AbstractType
{
    protected $referenceTypeSubscriber;
    protected $eventDispatcher;
    protected $referenceClass;

    /**
     * @param EventSubscriberInterface $referenceTypeSubscriber
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $referenceClass
     */
    public function __construct(
        EventSubscriberInterface $referenceTypeSubscriber,
        EventDispatcherInterface $eventDispatcher,
        $referenceClass
    ) {
        $this->referenceTypeSubscriber = $referenceTypeSubscriber;
        $this->eventDispatcher = $eventDispatcher;
        $this->referenceClass = $referenceClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'itkg_reference.form.reference.name',
                'group_id' => 'property',
                'sub_group_id' => 'information',
            ))
            ->add('keywords', 'oo_keywords_choice', array(
                'label' => 'itkg_reference.form.reference.keywords',
                'required' => false,
                'group_id' => 'property',
                'sub_group_id' => 'information',
            ));

        $builder->addEventSubscriber($this->referenceTypeSubscriber);
        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
        $this->eventDispatcher->dispatch(ReferenceFormEvents::REFERENCE_FORM_CREATION, new ReferenceFormEvent($builder));
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
        return 'itkg_reference';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->referenceClass,
            'delete_button' => false,
            'new_button' => false,
                'group_enabled' => true,
                'group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'itkg_reference.form.reference.group.property',
                    ),
                    'data' => array(
                        'rank' => 1,
                        'label' => 'itkg_reference.form.reference.group.data',
                    ),
                ),
                'sub_group_render' => array(
                    'information' => array(
                        'rank' => 0,
                        'label' => 'itkg_reference.form.reference.sub_group.information',
                    ),
                    'publication' => array(
                        'rank' => 1,
                        'label' => 'itkg_reference.form.reference.sub_group.publication',
                    ),
                    'data' => array(
                        'rank' => 0,
                        'label' => 'itkg_reference.form.reference.sub_group.data',
                    ),
                ),
        ));
    }
}
