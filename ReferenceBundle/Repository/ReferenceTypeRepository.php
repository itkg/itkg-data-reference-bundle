<?php

namespace Itkg\ReferenceBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Itkg\ReferenceInterface\Model\ReferenceTypeInterface;
use Itkg\ReferenceInterface\Repository\ReferenceTypeRepositoryInterface;

/**
 * Class ReferenceTypeRepository
 */
class ReferenceTypeRepository extends DocumentRepository implements ReferenceTypeRepositoryInterface
{
    /**
     * @return array
     */
    public function findAllByNotDeleted()
    {
        $qb = $this->createQueryBuilder('reference_type');
        $qb->field('deleted')->equals(false);

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $referenceTypeId
     *
     * @return ReferenceTypeInterface
     */
    public function findOneByReferenceTypeId($referenceTypeId)
    {
        $qb = $this->createQueryBuilder('reference_type');
        $qb->field('referenceTypeId')->equals($referenceTypeId);

        return $qb->getQuery()->getSingleResult();
    }
}
