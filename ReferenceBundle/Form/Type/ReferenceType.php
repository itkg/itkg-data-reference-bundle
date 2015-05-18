<?php

namespace Itkg\ReferenceBundle\Form\Type;

use OpenOrchestra\UserBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Itkg\ReferenceBundle\EventSubscriber\ReferenceTypeSubscriber;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use Itkg\ReferenceInterface\Repository\ReferenceTypeRepositoryInterface;
use Symfony\Component\Form\AbstractType;

/**
 * Class ReferenceType
 */
class ReferenceType extends AbstractType
{
    protected $referenceTypeRepository;
    protected $referenceClass;
    protected $contentAttributeClass;
    protected $translationChoiceManager;

    /**
     * @param ReferenceTypeRepositoryInterface $referenceTypeRepository
     * @param string                         $referenceClass
     * @param string                         $contentAttributeClass
     * @param TranslationChoiceManager       $translationChoiceManager
     */
    public function __construct(
        ReferenceTypeRepositoryInterface $referenceTypeRepository,
        $referenceClass,
        $contentAttributeClass,
        TranslationChoiceManager $translationChoiceManager
    )
    {
        $this->referenceTypeRepository = $referenceTypeRepository;
        $this->referenceClass = $referenceClass;
        $this->contentAttributClass = $contentAttributeClass;
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
                'label' => 'itkg_reference_bundle.form.reference.name'
            ));

        $builder->addEventSubscriber(new ReferenceTypeSubscriber(
            $this->referenceTypeRepository,
            $this->contentAttributeClass,
            $this->translationChoiceManager
        ));

        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'itkg_reference';
    }
}
