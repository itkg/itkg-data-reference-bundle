<?php

namespace Itkg\ReferenceInterface\Repository;

use Itkg\ReferenceInterface\Model\ReferenceInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\ModelInterface\Repository\RepositoryTrait\UseTrackableTraitInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\RepositoryTrait\AutoPublishableTraitInterface;

/**
 * Interface ReferenceRepositoryInterface
 */
interface ReferenceRepositoryInterface extends ReadReferenceRepositoryInterface, UseTrackableTraitInterface, AutoPublishableTraitInterface
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
     * @param string                      $siteId
     * @param string                      $language
     *
     * @return array
     */
    public function findForPaginateFilterByReferenceTypeSiteAndLanguage(PaginateFinderConfiguration $configuration, $referenceType, $siteId, $language);

    /**
     * @param string $referenceType
     * @param string $siteId
     * @param string $language
     *
     * @return int
     */
    public function countFilterByReferenceTypeSiteAndLanguage($referenceType, $siteId, $language);

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param string                      $referenceType
     * @param string                      $siteId
     * @param string                      $language
     *
     * @return int
     */
    public function countWithFilterAndReferenceTypeSiteAndLanguage(PaginateFinderConfiguration $configuration, $referenceType, $siteId, $language);

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
    public function findAllPublishedByReferenceId($referenceId);

    /**
     * @param string       $id
     * @param string       $siteId
     * @param array|null   $eventTypes
     * @param boolean|null $published
     * @param int|null     $limit
     * @param array|null   $sort
     * @param array        $referenceTypes
     *
     * @return array
     */
    public function findByHistoryAndSiteId(
        $id,
        $siteId,
        array $eventTypes = null,
        $published = null,
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
     * @param StatusInterface $status
     * @param string          $referenceType
     */
    public function updateStatusByReferenceType(StatusInterface $status, $referenceType);

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

    /**
     * @param string $referenceId
     *
     * @return int
     */
    public function hasReferenceIdWithoutAutoUnpublishToState($referenceId);
}
