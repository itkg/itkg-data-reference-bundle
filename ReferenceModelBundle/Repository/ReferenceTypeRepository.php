<?php

namespace Itkg\ReferenceModelBundle\Repository;

use Itkg\ReferenceInterface\Model\ReferenceTypeInterface;
use Itkg\ReferenceInterface\Repository\ReferenceTypeRepositoryInterface;
use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use Solution\MongoAggregation\Pipeline\Stage;

/**
 * Class ReferenceTypeRepository
 */
class ReferenceTypeRepository extends AbstractAggregateRepository implements ReferenceTypeRepositoryInterface
{
    use PaginationTrait;

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

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return array
     */
    public function findAllNotDeletedInLastVersionForPaginate(PaginateFinderConfiguration $configuration)
    {
        $qa = $this->createAggregateQueryNotDeletedSortedByTypeId();

        $qa = $this->generateFilter($qa, $configuration);

        $elementName = 'referenceType';
        $this->generateLastVersionFilter($qa, $elementName, $configuration);

        $qa = $this->generateFilterSort(
            $qa,
            $configuration->getOrder(),
            $configuration->getDescriptionEntity()
        );
        $qa = $this->generateSkipFilter($qa, $configuration->getSkip());
        $qa = $this->generateLimitFilter($qa, $configuration->getLimit());

        return $this->hydrateAggregateQuery($qa, $elementName, 'getReferenceTypeId');
    }

    /**
     * @param FinderConfiguration $configuration
     *
     * @return int
     */
    public function countNotDeletedInLastVersionWithSearchFilter(FinderConfiguration $configuration)
    {
        $qa = $this->createAggregateQueryNotDeletedSortedByTypeId();
        $qa = $this->generateFilter($qa, $configuration);

        $elementName = 'referenceType';
        $this->generateLastVersionFilter($qa, $elementName);

        return $this->countDocumentAggregateQuery($qa, $elementName);
    }

    /**
     * @return int
     */
    public function countByContentTypeInLastVersion()
    {
        $qa = $this->createAggregateQueryNotDeletedSortedByTypeId();
        $elementName = 'reference';
        $this->generateLastVersionFilter($qa, $elementName);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param Stage                            $qa
     * @param string                           $elementName
     * @param PaginateFinderConfiguration|null $configuration
     */
    protected function generateLastVersionFilter(Stage $qa, $elementName, $configuration = null)
    {
        $group = array();

        if (!is_null($configuration)) {
            $group = $this->generateGroupForFilterSort($configuration);
        }
        $group = array_merge(
            $group,
            array(
                '_id' => array('referenceTypeId' => '$referenceTypeId'),
                $elementName => array('$last' => '$$ROOT')
            )
        );

        $qa->sort(array('version' => 1));
        $qa->group($group);
    }

    /**
     * @return \Solution\MongoAggregation\Pipeline\Stage
     */
    protected function createAggregateQueryNotDeletedSortedByTypeId()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('deleted' => false));
        $qa->sort(array('referenceTypeId' => -1));

        return $qa;
    }
}
