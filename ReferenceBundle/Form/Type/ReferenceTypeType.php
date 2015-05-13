<?php

namespace Itkg\ReferenceBundle\Form\Type;

use Itkg\ReferenceInterface\Repository\ReferenceTypeRepositoryInterface;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Itkg\ReferenceBundle\Repository\ReferenceTypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ReferenceType
 */
class ReferenceType extends AbstractType
{
    protected $referenceTypeRepository;
    protected $referenceClass;
    protected $contentAttributClass;
    protected $translationChoiceManager;

    /**
     * @param ReferenceTypeRepositoryInterface $referenceTypeRepository
     * @param string                           $referenceClass
     * @param string                           $contentAttributClass
     * @param TranslationChoiceManager         $translationChoiceManager
     */
    public function __construct(
        ReferenceTypeRepositoryInterface $referenceTypeRepository,
        $referenceClass,
        $contentAttributClass,
        TranslationChoiceManager $translationChoiceManager
    )
    {
        $this->referenceTypeRepository = $referenceTypeRepository;
        $this->referenceClass = $referenceClass;
        $this->contentAttributClass = $contentAttributClass;
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'open_orchestra_backoffice.form.content.name'
            ));

        $builder->addEventSubscriber(new ReferenceTypeSubscriber(
            $this->referenceTypeRepository,
            $this->contentAttributClass,
            $this->translationChoiceManager
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
            'data_class' => $this->referenceClass,
        ));
    }
}
