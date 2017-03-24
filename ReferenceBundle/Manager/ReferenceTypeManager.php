<?php

namespace Itkg\ReferenceBundle\Manager;

use Itkg\ReferenceInterface\Model\ReferenceTypeInterface;

/**
 * Class ReferenceTypeManager
 */
class ReferenceTypeManager
{
    protected $referenceTypeClass;

    /**
     * @param string $referenceTypeClass
     */
    public function __construct($referenceTypeClass)
    {
        $this->referenceTypeClass = $referenceTypeClass;
    }

    /**
     * @return ReferenceTypeInterface
     */
    public function initializeNewReferenceType()
    {
        $referenceTypeClass = $this->referenceTypeClass;
        /** @var ReferenceTypeInterface $referenceType */
        $referenceType = new $referenceTypeClass();
        $referenceType->setDefaultListable($this->getDefaultListableColumns());

        return $referenceType;
    }

    /**
     * @param ReferenceTypeInterface $referenceType
     *
     * @return ReferenceTypeInterface
     */
    public function duplicate(ReferenceTypeInterface $referenceType)
    {
        $newReferenceType = clone $referenceType;

        foreach ($referenceType->getFields() as $field) {
            $newField = clone $field;
            foreach ($field->getOptions() as $option) {
                $newOption = clone $option;
                $newField->addOption($newOption);
            }

            $newReferenceType->addFieldType($newField);
        }

        return $newReferenceType;
    }

    /**
     * @param array $referenceTypes
     */
    public function delete($referenceTypes)
    {
        if (!empty($referenceTypes)) {
            foreach ($referenceTypes as $referenceType)
            {
                $referenceType->setDeleted(true);
            }
        }
    }

    /**
     * @return array
     */
    protected function getDefaultListableColumns()
    {
        return array(
            'name'           => true,
            'status_label'   => false,
            'linked_to_site' => true,
            'created_at'     => true,
            'created_by'     => true,
            'updated_at'     => false,
            'updated_by'     => false,
        );
    }
}
