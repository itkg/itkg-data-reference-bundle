<?php

namespace Itkg\ReferenceBundle\NavigationPanel\Strategies;

use Itkg\ReferenceInterface\Model\ReferenceTypeInterface;
use Itkg\ReferenceInterface\Repository\ReferenceTypeRepositoryInterface;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AbstractNavigationPanelStrategy;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ReferenceTypeForReferencePanelStrategy
 */
class ReferenceTypeForReferencePanelStrategy extends AbstractNavigationPanelStrategy
{
    const ROLE_ACCESS_REFERENCE_TYPE_FOR_REFERENCE = 'ROLE_ACCESS_REFERENCE_TYPE_FOR_REFERENCE';

    protected $translator;
    protected $referenceTypeRepository;
    protected $translationChoiceManager;

    /**
     * @param ReferenceTypeRepositoryInterface $referenceTypeRepository
     * @param array                            $dataParameter
     * @param TranslationChoiceManager         $translationChoiceManager
     * @param TranslatorInterface              $translator
     */
    public function __construct(
        ReferenceTypeRepositoryInterface $referenceTypeRepository,
        array $dataParameter,
        TranslationChoiceManager $translationChoiceManager,
        TranslatorInterface $translator
    )
    {
        $this->referenceTypeRepository = $referenceTypeRepository;
        $this->datatableParameter = $dataParameter;
        $this->translationChoiceManager = $translationChoiceManager;
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    public function show()
    {
        $datatableParameterNames = array();
        foreach ($this->getReferenceTypes() as $referenceType) {
            $datatableParameterNames[] = 'references_' . $referenceType->getReferenceTypeId();
        }

        return $this->render(
            'ItkgReferenceBundle:EditorialPanel:showReferenceTypeForReference.html.twig',
            array(
                'referenceTypes' => $this->getReferenceTypes(),
                'datatableParameterNames' => $datatableParameterNames,
            )
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'editorial';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reference_type_for_reference';
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return self::ROLE_ACCESS_REFERENCE_TYPE_FOR_REFERENCE;
    }

    /**
     * @return array
     */
    public function getDatatableParameter()
    {
        if ($this->translator instanceof TranslatorInterface) {
            $this->datatableParameter = $this->preFormatDatatableParameter($this->datatableParameter, $this->translator);
        }

        $dataParameter = array();

        /** @var ReferenceTypeInterface $referenceType */
        foreach ($this->getReferenceTypes() as $referenceType) {
            $referenceTypeId = 'references_' . $referenceType->getReferenceTypeId();
            $dataParameter[$referenceTypeId] = $this->datatableParameter;
            foreach ($dataParameter[$referenceTypeId] as &$parameter) {
                $parameter['visible'] = true;
            }
            /** @var FieldTypeInterface $field */
            foreach ($referenceType->getFields() as $field) {
                $dataParameter[$referenceTypeId][] = array(
                    'name' => 'attributes.' . $field->getFieldId() . '.string_value',
                    'title' => $this->translationChoiceManager->choose($field->getLabels()),
                    'visible' => $field->getListable() === true,
                    'activateColvis' => true,
                    'searchField' => $field->getFieldTypeSearchable(),
                );
            }
        }

        return $dataParameter;
    }

    /**
     * @return array
     */
    protected function getReferenceTypes()
    {
        $referenceTypes = $this->referenceTypeRepository->findAllByNotDeleted();

        return $referenceTypes;
    }
}
