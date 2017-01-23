<?php

namespace Itkg\ReferenceBundle\Tests\Form\Type;

use Itkg\ReferenceBundle\Form\Type\ReferenceTypeType;
use OpenOrchestra\Backoffice\EventListener\TranslateValueInitializerListener;
use Phake;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ReferenceTypeTypeTest
 */
class ReferenceTypeTypeTest extends \PHPUnit_Framework_TestCase
{

    protected $referenceTypeClass;
    protected $translator;
    protected $translateValueInitializer;
    protected $form;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->referenceTypeClass = 'Itkg\ReferenceModelBundle\Document\ReferenceType';
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->translateValueInitializer = Phake::mock('OpenOrchestra\Backoffice\EventListener\TranslateValueInitializerListener');

        $this->form = new ReferenceTypeType($this->referenceTypeClass, $this->translator, $this->translateValueInitializer);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Itkg\ReferenceBundle\Form\Type\ReferenceTypeType', $this->form);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('itkg_reference_type', $this->form->getName());
    }

    /**
     * Test build Form
     */
    public function testBuildForm()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventListener(Phake::anyParameters())->thenReturn($builder);
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn('foo');
        $this->form->buildForm($builder, array());
        $referenceTypeIdName = 'referenceTypeId';

        Phake::verify($builder->addEventListener(FormEvents::PRE_SET_DATA, array($this->translateValueInitializer, 'preSetData')));
        Phake::verify($builder)
            ->add($referenceTypeIdName, 'text', array(
                    'label' => 'itkg_reference_bundle.form.reference_type.id',
                    'attr' => array(
                        'class' => 'generate-id-dest',
                    )
                ));
        Phake::verify($builder)
            ->add('names', 'oo_translated_value_collection', array(
                    'label' => 'itkg_reference_bundle.form.reference_type.names'
                ));
        Phake::verify($builder)
            ->add('template', 'text', array(
                    'label' => 'open_orchestra_backoffice.form.content_type.template.label',
                    'required' => false,
                    'attr' => array('help_text' => 'open_orchestra_backoffice.form.content_type.template.helper'),
                ));
        Phake::verify($builder)
            ->add('fields', 'collection', array(
                    'type' => 'oo_field_type',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'itkg_reference_bundle.form.reference_type.fields',
                    'attr' => array(
                        'data-prototype-label-add' => 'foo',
                        'data-prototype-label-new' => 'foo',
                        'data-prototype-label-remove' => 'foo',
                        'data-prototype-callback-add' => "checkReferenceTypeId('#". $this->form->getName() . "_". $referenceTypeIdName . "')",
                        'data-prototype-callback-error-message' => 'foo'
                    )
                ));
    }
}
