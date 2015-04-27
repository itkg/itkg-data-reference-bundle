<?php

namespace Itkg\ReferenceBundle\Manager;

use Itkg\ReferenceInterface\Model\ReferenceTypeInterface;

/**
 * Class ReferenceTypeManager
 */
class ReferenceTypeManager
{
    /**
     * @param ReferenceTypeInterface $referenceType
     *
     * @return ReferenceTypeInterface
     */
    public function duplicate(ReferenceTypeInterface $referenceType)
    {
        $newReferenceType = clone $referenceType;

        foreach ($referenceType->getNames() as $name) {
            $newName = clone $name;
            $newReferenceType->addName($newName);
        }
        foreach ($referenceType->getFields() as $field) {
            $newField = clone $field;
            foreach ($field->getLabels() as $label) {
                $newLabel = clone $label;
                $newField->addLabel($newLabel);
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
}
