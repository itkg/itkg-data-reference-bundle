<?php

namespace Itkg\ReferenceModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use OpenOrchestra\ModelInterface\Model\ReferenceTypeInterface;
use Itkg\ReferenceInterface\Repository\ReferenceTypeRepositoryInterface;

/**
 * Class UniqueReferenceTypeValidator
 */
class UniqueReferenceTypeIdValidator extends ConstraintValidator
{
    protected $repository;

    /**
     * @param ReferenceTypeRepositoryInterface $repository
     */
    public function __construct(ReferenceTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param ReferenceTypeInterface           $value
     * @param UniqueReferenceTypeId|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ((int)$value->getVersion() < 1) {
            $result = $this->repository->findOneByReferenceTypeIdInLastVersion($value->getReferenceTypeId());

            if (null !== $result) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('referenceTypeId')
                    ->addViolation();
            }
        }
    }
}
