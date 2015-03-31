<?php

namespace itkg\ReferenceBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use itkg\ReferenceInterface\Repository\ReferenceTypeRepositoryInterface;

/**
 * Class ReferenceTypeRepository
 */
class ReferenceTypeRepository extends DocumentRepository implements ReferenceTypeRepositoryInterface
{
    /**
     * @return array
     */
    public function findAll()
    {
        return array();
    }
}
