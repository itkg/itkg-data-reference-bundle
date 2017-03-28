<?php

namespace Itkg\ReferenceModelBundle\Repository;

use OpenOrchestra\Repository\AbstractAggregateRepository;
use Itkg\ReferenceInterface\Model\ReferenceInterface;
use Itkg\ReferenceInterface\Repository\ReferenceRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\ModelInterface\Repository\RepositoryTrait\KeywordableTraitInterface;
use Solution\MongoAggregation\Pipeline\Stage;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\KeywordableTrait;
use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\UseTrackableTrait;
use OpenOrchestra\Pagination\MongoTrait\FilterTrait;
use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\AutoPublishableTrait;
use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies\StringFilterStrategy;
use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies\DateFilterStrategy;

/**
 * Class ReferenceRepository
 */
class ReferenceRepository  extends AbstractAggregateRepository implements FieldAutoGenerableRepositoryInterface, ReferenceRepositoryInterface, KeywordableTraitInterface
{
    use KeywordableTrait;
    use UseTrackableTrait;
    use FilterTrait;
    use AutoPublishableTrait;

    const ALIAS_FOR_GROUP = 'reference';

    /**
     * @param string $referenceId
     *
     * @return boolean
     */
    public function testUniquenessInContext($referenceId)
    {
        return $this->findOneByReferenceId($referenceId) !== null;
    }

    /**
     * @param string $referenceId
     *
     * @return ReferenceInterface
     */
    public function findOneByReferenceId($referenceId)
    {
        return $this->findOneBy(array('referenceId' => $referenceId));
    }

    /**
     * @param string $referenceId
     * @param string $language
     *
     * @return ReferenceInterface
     */
    public function findPublishedVersion($referenceId, $language)
    {
        $qa = $this->createAggregationQueryWithLanguageAndPublished($language);

        $qa->match(array('referenceId' => $referenceId));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param string      $language
     * @param string      $referenceType
     * @param string      $choiceType
     * @param string|null $condition
     * @param string|null $siteId
     *
     * @return array
     */
    public function findByReferenceTypeAndCondition($language, $referenceType = '', $choiceType = self::CHOICE_AND, $condition = null, $siteId = null)
    {
        $qa = $this->createAggregationQuery();
        $qa->match($this->generateFilterPublishedNotDeletedOnLanguage($language));
        if (!is_null($siteId)) {
            $qa->match($this->generateSiteIdFilter($siteId));
        }
        $filter = $this->generateReferenceTypeFilter($referenceType);

        if ($filter && $condition) {
            $qa->match($this->appendFilters($filter, $this->transformConditionToMongoCondition($condition), $choiceType));
        } elseif ($filter) {
            $qa->match($filter);
        } elseif ($condition) {
            $qa->match($this->transformConditionToMongoCondition($condition));
        }

        return $this->hydrateAggregateQuery($qa, self::ALIAS_FOR_GROUP);
    }

    /**
     * Generate filter on visible published references in $language
     *
     * @param string $language
     *
     * @return array
     */
    protected function generateFilterPublishedNotDeletedOnLanguage($language)
    {
        return array(
            'language' => $language,
            'deleted' => false,
            'status.publishedState' => true
        );
    }

    /**
     * Generate Reference Type filter
     *
     * @param string|null $referenceType
     *
     * @return array|null
     */
    protected function generateReferenceTypeFilter($referenceType)
    {
        $filter = null;

        if (!is_null($referenceType) && '' != $referenceType) {
            $filter = array('referenceType' => $referenceType);
        }

        return $filter;
    }

    /**
     * Append two filters according to $choiceType operator
     *
     * @param array  $filter1
     * @param array  $filter2
     * @param string $choiceType
     *
     * @return array
     */
    protected function appendFilters($filter1, $filter2, $choiceType)
    {
        $choiceOperatior = '$and';
        if (self::CHOICE_OR == $choiceType) {
            $choiceOperatior = '$or';
        }

        return array($choiceOperatior => array($filter1, $filter2));
    }

    /**
     * @param string $referenceId
     * @param string $language
     *
     * @return array
     */
    public function findNotDeletedSortByUpdatedAt($referenceId, $language)
    {
        $qa = $this->createAggregationQueryWithLanguage($language);
        $qa->match(
            array(
                'referenceId' => $referenceId,
                'deleted'   => false,
            )
        );
        $qa->sort(array('updatedAt' => -1));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $referenceId
     * @param string $language
     *
     * @return array
     */
    public function countNotDeletedByLanguage($referenceId, $language)
    {
        $qa = $this->createAggregationQueryWithLanguage($language);
        $qa->match(
            array(
                'referenceId' => $referenceId,
                'deleted'   => false,
            )
        );

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string $referenceId
     *
     * @return array
     */
    public function findByReferenceId($referenceId)
    {
        return $this->findBy(array('referenceId' => $referenceId));
    }

    /**
     * @param string      $referenceId
     * @param string      $language
     *
     * @return ReferenceInterface|null
     */
    public function findOneByLanguage($referenceId, $language)
    {
        $qa = $this->createAggregationQueryWithReferenceIdAndLanguage($referenceId, $language);

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param string                      $referenceType
     * @param string                      $siteId
     * @param string                      $language
     * @param array                       $searchTypes
     *
     * @return array
     */
    public function findForPaginateFilterByReferenceTypeSiteAndLanguage(PaginateFinderConfiguration $configuration, $referenceType, $siteId, $language, array $searchTypes = array())
    {
        $qa = $this->createAggregateQueryWithDeletedFilter(false);
        $qa->match($this->generateReferenceTypeFilter($referenceType));
        $qa->match($this->generateSiteIdFilter($siteId));
        $qa->match($this->generateLanguageFilter($language));

        $this->filterSearch($configuration, $qa, $searchTypes);

        $order = $configuration->getOrder();

        $newOrder = array();
        array_walk($order, function($item, $key) use(&$newOrder) {
            $newOrder[str_replace('.', '_', $key)] = $item;
        });

        if (!empty($newOrder)) {
            $qa->sort($newOrder);
        }

        $qa->skip($configuration->getSkip());
        $qa->limit($configuration->getLimit());

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $referenceType
     * @param string $siteId
     * @param string $language
     *
     * @return int
     */
    public function countFilterByReferenceTypeSiteAndLanguage($referenceType, $siteId, $language)
    {
        return $this->countInContextByReferenceTypeSiteAndLanguage($referenceType, $siteId, $language);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param string                      $referenceType
     * @param string                      $siteId
     * @param string                      $language
     * @param array                       $searchTypes
     *
     * @return int
     */
    public function countWithFilterAndReferenceTypeSiteAndLanguage(PaginateFinderConfiguration $configuration, $referenceType, $siteId, $language, array $searchTypes = array())
    {
        return $this->countInContextByReferenceTypeSiteAndLanguage($referenceType, $siteId, $language, $configuration, $searchTypes);
    }

    /**
     * @param string $referenceType
     *
     * @return int
     */
    public function countByReferenceType($referenceType)
    {
        $qa = $this->createAggregateQueryWithReferenceTypeFilter($referenceType);

        return $this->countDocumentAggregateQuery($qa);
    }

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
    ) {
        $qa = $this->createAggregationQuery();
        $filter = array(
            'histories.user.$id' => new \MongoId($id),
            'deleted' => false
        );
        $qa->match($this->generateSiteIdFilter($siteId));
        if (null !== $eventTypes) {
            $filter['histories.eventType'] = array('$in' => $eventTypes);
        }
        if (null !== $published) {
            $filter['status.published'] = $published;
        }
        if (!empty($referenceTypes)) {
            $filter['referenceType'] = array('$in' => $referenceTypes);
        }

        $qa->match($filter);

        if (null !== $limit) {
            $qa->limit($limit);
        }

        if (null !== $sort) {
            $qa->sort($sort);
        }

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $entityId
     *
     * @return ReferenceInterface
     */
    public function findById($entityId)
    {
        return $this->find(new \MongoId($entityId));
    }

    /**
     * @param string $siteId
     *
     * @return array
     */
    protected function generateSiteIdFilter($siteId)
    {
        return array('siteId' => $siteId);
    }

    /**
     * @param string $language
     *
     * @return array
     */
    protected function generateLanguageFilter($language)
    {
        return array('language' => $language);
    }

    /**
     * @param $referenceType
     *
     * @return \Solution\MongoAggregation\Pipeline\Stage
     */
    protected function createAggregateQueryWithReferenceTypeFilter($referenceType)
    {
        $qa = $this->createAggregationQuery();

        if ($referenceType) {
            $qa->match(array('referenceType' => $referenceType));
        }

        return $qa;
    }

    /**
     * @param string $language
     *
     * @return Stage
     */
    protected function createAggregationQueryWithLanguage($language)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('language' => $language));

        return $qa;
    }

    /**
     * @param string      $referenceId
     * @param string      $language
     *
     * @return Stage
     */
    protected function createAggregationQueryWithReferenceIdAndLanguage($referenceId, $language)
    {
        $qa = $this->createAggregationQueryWithLanguage($language);
        $qa->match(
            array(
                'referenceId' => $referenceId
            )
        );
        $qa->sort(array('createdAt' => -1));

        return $qa;
    }

    /**
     * @param string $language
     *
     * @return Stage
     */
    protected function createAggregationQueryWithLanguageAndPublished($language)
    {
        $qa = $this->createAggregationQueryWithLanguage($language);
        $qa->match(
            array(
                'deleted'               => false,
                'status.publishedState' => true,
            )
        );

        return $qa;
    }

    /**
     * @param string $referenceId
     * @param string $language
     * @param string $siteId
     *
     * @return ReferenceInterface
     */
    public function findOnePublished($referenceId, $language, $siteId)
    {
        $qa = $this->createAggregationQueryWithLanguageAndPublished($language);
        $filter['referenceId'] = $referenceId;
        $qa->match($filter);

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param string $referenceId
     *
     * @return array
     */
    public function findAllPublishedByReferenceId($referenceId)
    {
        $qa = $this->createAggregationQuery();
        $filter['status.publishedState'] = true;
        $filter['deleted'] = false;
        $filter['referenceId'] = $referenceId;
        $qa->match($filter);

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param StatusableInterface $element
     *
     * @return array
     */
    public function findPublished(StatusableInterface $element)
    {
        $qa = $this->createAggregationQueryWithLanguageAndPublished($element->getLanguage());
        $qa->match(array('referenceId' => $element->getReferenceId()));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param StatusInterface $status
     * @param string          $referenceType
     *
     * @return array
     */
    public function updateStatusByReferenceType(StatusInterface $status, $referenceType) {
        $this->createQueryBuilder()
            ->updateMany()
            ->field('status')->set($status)
            ->field('referenceType')->equals($referenceType)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $referenceId
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function softDeleteReference($referenceId)
    {
        $qb = $this->createQueryBuilder();
        $qb->updateMany()
            ->field('referenceId')->equals($referenceId)
            ->field('deleted')->set(true)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $referenceId
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function restoreDeletedReference($referenceId)
    {
        $qb = $this->createQueryBuilder();
        $qb->updateMany()
            ->field('referenceId')->equals($referenceId)
            ->field('deleted')->set(false)
            ->getQuery()
            ->execute();
    }

    /**
     * @param array $ids
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function removeReferences(array $ids)
    {
        $referenceMongoIds = array();
        foreach ($ids as $id) {
            $referenceMongoIds[] = new \MongoId($id);
        }

        $qb = $this->createQueryBuilder();
        $qb->remove()
            ->field('id')->in($referenceMongoIds)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $referenceId
     *
     * @return int
     */
    public function hasReferenceIdWithoutAutoUnpublishToState($referenceId)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'referenceId'  => $referenceId,
                'status.autoUnpublishToState' => false
            )
        );

        return 0 !== $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param Stage                       $qa
     * @param array                       $searchTypes
     *
     * @return Stage
     */
    protected function filterSearch(PaginateFinderConfiguration $configuration, Stage $qa, array $searchTypes)
    {
        $qa = $this->generateFilter($configuration, $qa, StringFilterStrategy::FILTER_TYPE, 'name', 'name');
        $language = $configuration->getSearchIndex('language');
        if (null !== $language && $language !== '') {
            $qa->match(array('language' => $language));
        }
        $status = $configuration->getSearchIndex('status');
        if (null !== $status && $status !== '') {
            $qa->match(array('status._id' => new \MongoId($status)));
        }
        $qa = $this->generateFilter($configuration, $qa, DateFilterStrategy::FILTER_TYPE, 'created_at', 'createdAt', $configuration->getSearchIndex('date_format'));
        $qa = $this->generateFilter($configuration, $qa, StringFilterStrategy::FILTER_TYPE, 'created_by', 'createdBy');
        $qa = $this->generateFilter($configuration, $qa, DateFilterStrategy::FILTER_TYPE, 'updated_at', 'updatedAt', $configuration->getSearchIndex('date_format'));
        $qa = $this->generateFilter($configuration, $qa, StringFilterStrategy::FILTER_TYPE, 'updated_by', 'updatedBy');
        $qa = $this->generateFieldsFilter($configuration, $qa, $searchTypes);

        return $qa;
    }

    /**
     * @param $deleted
     *
     * @return \Solution\MongoAggregation\Pipeline\Stage
     */
    protected function createAggregateQueryWithDeletedFilter($deleted)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('deleted' => $deleted));

        return $qa;
    }

    /**
     * @param string                      $referenceType
     * @param string                      $siteId
     * @param string                      $language
     * @param array                       $searchTypes
     * @param PaginateFinderConfiguration $configuration
     *
     * @return int
     */
    protected function countInContextByReferenceTypeSiteAndLanguage($referenceType, $siteId, $language, PaginateFinderConfiguration $configuration = null, array $searchTypes = array())
    {
        $qa = $this->createAggregateQueryWithDeletedFilter(false);
        $qa->match($this->generateReferenceTypeFilter($referenceType));
        $qa->match($this->generateSiteIdFilter($siteId));
        $qa->match($this->generateLanguageFilter($language));

        if (!is_null($configuration)) {
            $this->filterSearch($configuration, $qa, $searchTypes);
        }

        return $this->countDocumentAggregateQuery($qa, self::ALIAS_FOR_GROUP);
    }
}
