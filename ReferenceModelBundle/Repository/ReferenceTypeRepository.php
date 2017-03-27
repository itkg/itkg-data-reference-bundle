<?php

namespace Itkg\ReferenceModelBundle\Repository;

use Solution\MongoAggregation\Pipeline\Stage;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use Itkg\ReferenceInterface\Repository\ReferenceTypeRepositoryInterface;

/**
 * Class ReferenceTypeRepository
 */
class ReferenceTypeRepository extends AbstractAggregateRepository implements ReferenceTypeRepositoryInterface
{
    /**
     * @param $language
     *
     * @return array
     */
    public function findAllNotDeletedInLastVersion($language = null)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'deleted' => false
            )
        );
        $elementName = 'referenceType';
        $this->generateLastVersionFilter($qa, $elementName);

        if ($language) {
            $qa->sort(
                array(
                    $elementName . '.names.' . $language. '.value' => 1
                )
            );
        }

        return $this->hydrateAggregateQuery($qa, $elementName, 'getReferenceTypeId');
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return array
     */
    public function findAllNotDeletedInLastVersionForPaginate(PaginateFinderConfiguration $configuration)
    {
        $qa = $this->createAggregateQueryNotDeletedInLastVersion();
        $filters = $this->getFilterSearch($configuration);
        if (!empty($filters)) {
            $qa->match($filters);
        }
        $elementName = 'referenceType';
        $group = array(
            'names' => array('$last' => '$names'),
            'referenceTypeId' => array('$last' => '$referenceTypeId')
        );
        $this->generateLastVersionFilter($qa, $elementName, $group);

        $order = $configuration->getOrder();
        if (!empty($order)) {
            $qa->sort($order);
        }

        $qa->skip($configuration->getSkip());
        $qa->limit($configuration->getLimit());

        return $this->hydrateAggregateQuery($qa, $elementName, 'getReferenceTypeId');
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return int
     */
    public function countNotDeletedInLastVersionWithSearchFilter(PaginateFinderConfiguration $configuration)
    {
        $qa = $this->createAggregateQueryNotDeletedInLastVersion();
        $filters = $this->getFilterSearch($configuration);
        if (!empty($filters)) {
            $qa->match($filters);
        }
        $elementName = 'referenceType';
        $this->generateLastVersionFilter($qa, $elementName);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @return int
     */
    public function countByReferenceTypeInLastVersion()
    {
        $qa = $this->createAggregateQueryNotDeletedInLastVersion();
        $elementName = 'reference';
        $this->generateLastVersionFilter($qa, $elementName);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string $referenceType
     *
     * @return ReferenceTypeInterface
     */
    public function findOneByReferenceTypeIdInLastVersion($referenceType)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('referenceTypeId' => $referenceType));
        $qa->sort(array('version' => -1));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param array $referenceTypeIds
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function removeByReferenceTypeId(array $referenceTypeIds)
    {
        $qb = $this->createQueryBuilder();
        $qb->updateMany()
            ->field('referenceTypeId')->in($referenceTypeIds)
            ->field('deleted')->set(true)
            ->getQuery()
            ->execute();
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return array
     */
    protected function getFilterSearch(PaginateFinderConfiguration $configuration) {
        $filter = array();
        $name = $configuration->getSearchIndex('name');
        $language = $configuration->getSearchIndex('language');
        if (null !== $name && $name !== '' && null !== $language && $language !== '' ) {
            $filter['names.' . $language] = new \MongoRegex('/.*'.$name.'.*/i');
        }

        $linkedToSite = $configuration->getSearchIndex('linkedToSite');
        if (null !== $linkedToSite && $linkedToSite !== '') {
            $filter['linkedToSite'] = (boolean) $linkedToSite;
        }

        $referenceTypeId = $configuration->getSearchIndex('referenceTypeId');
        if (null !== $referenceTypeId && $referenceTypeId !== '') {
            $filter['referenceTypeId'] =new \MongoRegex('/.*'.$referenceTypeId.'.*/i');
        }

        return $filter;
    }

    /**
     * @param Stage  $qa
     * @param string $elementName
     * @param string $elementName
     * @param array  $group
     */
    protected function generateLastVersionFilter(Stage $qa, $elementName, $group = array())
    {
        $group = array_merge($group, array(
                '_id' => array('referenceTypeId' => '$referenceTypeId'),
                $elementName => array('$last' => '$$ROOT')
        ));

        $qa->sort(array('version' => 1));
        $qa->group($group);
    }

    /**
     * @return \Solution\MongoAggregation\Pipeline\Stage
     */
    protected function createAggregateQueryNotDeletedInLastVersion()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('deleted' => false));
        $qa->sort(array('referenceTypeId' => -1));

        return $qa;
    }
}
