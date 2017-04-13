<?php

namespace Itkg\ReferenceModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueReferenceTypeId
 */
class UniqueReferenceTypeId extends Constraint
{
    public $message = 'itkg_reference_validators.document.reference_type.unique_reference_type_id';

    /**
     * @return string|void
     */
    public function validatedBy()
    {
        return 'unique_reference_type_id';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
