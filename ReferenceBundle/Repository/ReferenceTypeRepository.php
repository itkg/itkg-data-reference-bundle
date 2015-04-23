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
        $qb->field('deleted')->equals(false);

        return $qb->getQuery()->execute();
    }
}
