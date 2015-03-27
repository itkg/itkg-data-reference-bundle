<?php

namespace itkg\ReferenceInterface\Repository;

/**
 * Interface ReferenceTypeRepositoryInterface
 */
interface ReferenceTypeRepositoryInterface
{
    /**
     * @param string   $referenceType
     * @param int|null $version
     *
     * @return array|null|object
     */
    public function findOneByReferenceTypeIdAndVersion($referenceType, $version = null);

    /**
     * @return array
     */
    public function findAllByDeletedInLastVersion();

    /**
     * @return array
     */
    public function findAll();
}
