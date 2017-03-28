<?php

namespace Itkg\ReferenceInterface\Repository;

use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;

/**
 * Interface ReferenceTypeRepositoryInterface
 */
interface ReferenceTypeRepositoryInterface
{
    /**
     * @param array $referenceTypes
     *
     * @return array
     */
    public function findAllNotDeletedInLastVersion(array $referenceTypes = array());

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return array
     */
    public function findAllNotDeletedInLastVersionForPaginate(PaginateFinderConfiguration $configuration);

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return int
     */
    public function countNotDeletedInLastVersionWithSearchFilter(PaginateFinderConfiguration $configuration);

    /**
     * @return int
     */
    public function countByReferenceTypeInLastVersion();

    /**
     * @return array
     */
    public function findAll();

    /**
     * @param string $referenceType
     *
     * @return ReferenceTypeInterface
     */
    public function findOneByReferenceTypeIdInLastVersion($referenceType);

    /**
     * @param array $referenceTypeIds
     *
     * @throws \Exception
     */
    public function removeByReferenceTypeId(array $referenceTypeIds);

    /**
     * @param string $id
     *
     * @return ReferenceTypeInterface
     */
    public function find($id);
}
