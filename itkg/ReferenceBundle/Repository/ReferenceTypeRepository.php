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
     * @param string   $referenceType
     * @param int|null $version
     * 
     * @return array|null|object
     */
    public function findOneByReferenceTypeIdAndVersion($referenceType, $version = null)
    {
        $qb = $this->createQueryBuilder('n');
        $qb->field('referenceTypeId')->equals($referenceType);

        $qb->sort('version', 'desc');
        if ($version) {
            $qb->field('version')->equals($version);
        }

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @return array
     */
    public function findAllByDeletedInLastVersion()
    {
        $qb = $this->createQueryBuilder('c');
        $qb->field('deleted')->equals(false);

        $list = $qb->getQuery()->execute();
        $referenceTypes = array();

        foreach ($list as $referenceType) {
            if (empty($referenceTypes[$referenceType->getReferenceTypeId()])) {
                $referenceTypes[$referenceType->getReferenceTypeId()] = $referenceType;
            }
            if ($referenceTypes[$referenceType->getReferenceTypeId()]->getVersion() < $referenceType->getVersion()) {
                $referenceTypes[$referenceType->getReferenceTypeId()] = $referenceType;
            }
        }

        return $referenceTypes;
    }
}
