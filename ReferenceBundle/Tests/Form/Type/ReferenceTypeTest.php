<?php

namespace Itkg\ReferenceBundle\Tests\Form\Type;

use Itkg\ReferenceBundle\Form\Type\ReferenceType;
use Phake;

/**
 * Class ReferenceTypeTest
 */
class ReferenceTypeTest extends \PHPUnit_Framework_TestCase
{

    protected $referenceTypeRepository;
    protected $referenceClass;
    protected $contentAttributeClass;
    protected $translationChoiceManager;
    protected $referenceTypeSubscriberClass;
    protected $fieldTypesConfiguration;
    protected $valueTransformerManager;
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->referenceTypeRepository = Phake::mock('Itkg\ReferenceModelBundle\Repository\ReferenceTypeRepository');
        $this->referenceClass = 'Itkg\ReferenceModelBundle\Document\Reference';
        $this->contentAttributeClass = 'OpenOrchestra\ModelBundle\Document\ContentAttribute';
        $this->translationChoiceManager = Phake::mock('OpenOrchestra\Backoffice\Manager\TranslationChoiceManager');
        $this->referenceTypeSubscriberClass = 'Itkg\ReferenceBundle\EventSubscriber\ReferenceTypeSubscriber';
        $this->fieldTypesConfiguration = array();
        $this->valueTransformerManager = Phake::mock('OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerManager');

        $this->form = new ReferenceType(
            $this->referenceTypeRepository,
            $this->referenceClass,
            $this->contentAttributeClass,
            $this->translationChoiceManager,
            $this->referenceTypeSubscriberClass,
            $this->fieldTypesConfiguration,
            $this->valueTransformerManager
        );
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Itkg\ReferenceBundle\Form\Type\ReferenceType', $this->form);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('itkg_reference', $this->form->getName());
    }

    /**
     * Test build Form
     */
    public function testBuildForm()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);
        $this->form->buildForm($builder, array());
        Phake::verify($builder)
            ->add('name', 'text', array(
                    'label' => 'itkg_reference_bundle.form.reference.name'
                ));

        Phake::verify($builder->addEventSubscriber(new $this->referenceTypeSubscriberClass(
                $this->referenceTypeRepository,
                $this->contentAttributeClass,
                $this->translationChoiceManager,
                $this->fieldTypesConfiguration
            )));
    }
}
