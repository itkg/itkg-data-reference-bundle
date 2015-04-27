<?php

namespace Itkg\ReferenceBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Itkg\ReferenceInterface\Repository\ReferenceTypeRepositoryInterface;

/**
 * Class ReferenceTypeRepository
 */
class ReferenceTypeRepository extends DocumentRepository implements ReferenceTypeRepositoryInterface
{
    /**
     * @return array
     */
    public function findAllByDeleted()
    {
        $qb = $this->createQueryBuilder('reference_type');

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $referenceTypeId
     * 
     * @return array
     */
    public function findOneByreferenceTypeId($referenceTypeId)
    {
        $qb = $this->createQueryBuilder('reference_type');
        $qb->field('referenceTypeId')->equals($referenceTypeId);

        return $qb->getQuery()->getSingleResult();
    }
}
