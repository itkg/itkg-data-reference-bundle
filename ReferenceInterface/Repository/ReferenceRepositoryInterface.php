<?php

namespace Itkg\ReferenceInterface\Repository;

use Itkg\ReferenceInterface\Model\ReferenceInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\ModelInterface\Repository\RepositoryTrait\UseTrackableTraitInterface;

/**
 * Interface ReferenceRepositoryInterface
 */
interface ReferenceRepositoryInterface extends ReadReferenceRepositoryInterface, UseTrackableTraitInterface
{
    /**
     * @return array list of news
     */
    public function findAll();

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function testUniquenessInContext($name);

    /**
     * @param string $referenceId
     * @param string $language
     *
     * @return array
     */
    public function findNotDeletedSortByUpdatedAt($referenceId, $language);

    /**
     * @param string $referenceId
     * @param string $language
     *
     * @return array
     */
    public function countNotDeletedByLanguage($referenceId, $language);

    /**
     * @param string $referenceId
     *
     * @return array
     */
    public function findByReferenceId($referenceId);

    /**
     * @param string   $referenceId
     * @param string   $language
     *
     * @return ReferenceInterface|null
     */
    public function findOneByLanguage($referenceId, $language);

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param string                      $referenceType
     * @param string                      $language
     *
     * @return array
     */
    public function findForPaginateFilterByReferenceTypeAndLanguage(PaginateFinderConfiguration $configuration, $referenceType, $language);

    /**
     * @param string $referenceType
     * @param string $language
     *
     * @return int
     */
    public function countFilterByReferenceTypeAndLanguage($referenceType, $language);

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param string                      $referenceType
     * @param string                      $language
     *
     * @return int
     */
    public function countWithFilterAndReferenceTypeAndLanguage(PaginateFinderConfiguration $configuration, $referenceType, $language);

    /**
     * @param string $referenceType
     *
     * @return int
     */
    public function countByReferenceType($referenceType);

    /**
     * @param string $referenceId
     *
     * @return array
     */
    public function findAllByReferenceId($referenceId);

    /**
     * @param string       $id
     * @param array|null   $eventTypes
     * @param int|null     $limit
     * @param array|null   $sort
     * @param array        $referenceTypes
     *
     * @return array
     */
    public function findByHistory(
        $id,
        array $eventTypes = null,
        $limit = null,
        array $sort = null,
        array $referenceTypes = array()
    );

    /**
     * @param string $entityId
     *
     * @return ReferenceInterface
     */
    public function findById($entityId);

    /**
     * @param array $ids
     */
    public function removeReferences(array $ids);

    /**variab
     * @param string $referenceId
     *
     * @throws \Exception
     */
    public function softDeleteReference($referenceId);

    /**
     * @param $referenceId
     *
     * @throws \Exception
     */
    public function restoreDeletedReference($referenceId);
}
