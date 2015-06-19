<?php

namespace Itkg\ReferenceBundle\Repository;

use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\PaginateAndSearchFilterTrait;
use OpenOrchestra\ModelBundle\Repository\AbstractRepository;
use Itkg\ReferenceInterface\Model\ReferenceInterface;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
use Itkg\ReferenceInterface\Repository\ReferenceRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Class ReferenceRepository
 */
class ReferenceRepository extends AbstractRepository implements FieldAutoGenerableRepositoryInterface, ReferenceRepositoryInterface
{
    use PaginateAndSearchFilterTrait;

    /**
     * @var CurrentSiteIdInterface
     */
    protected $currentSiteManager;

    /**
     * @param CurrentSiteIdInterface $currentSiteManager
     */
    public function setCurrentSiteManager(CurrentSiteIdInterface $currentSiteManager)
    {
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function testUniquenessInContext($name)
    {
        return $this->findOneBy(array('name' => $name)) !== null;
    }

    /**
     * @param string|null $language
     *
     * @return Builder
     */
    protected function createQueryBuilderWithLanguage($language = null)
    {
        $qb = $this->createQueryBuilder('reference');
        if($language != null)
        {
            $qb->field('language')->equals($language);
        }
        $qb->field('deleted')->equals(false);

        return $qb;
    }

    /**
     * @param string $referenceId
     *
     * @return ReferenceInterface
     */
    public function findOneByIdAndLanguageNotDeleted($referenceId, $language = null)
    {
        $qb = $this->createQueryBuilderWithLanguage($language);

        $qb->field('referenceId')->equals($referenceId);

        return $qb->getQuery()->getSingleResult();
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
    public function findOneByReferenceIdAndLanguage($referenceId, $language)
    {
        return $this->findOneBy(array('referenceId' => $referenceId, 'language' => $language));
    }

    /**
     * @param string $referenceType
     *
     * @return ReferenceInterface
     */
    public function findByReferenceTypeNotDeleted($referenceType = null)
    {
        $qb = $this->createQueryBuilder('reference');

        if ($referenceType) {
            $qb->field('referenceTypeId')->equals($referenceType);
        }

        $qb->field('deleted')->equals(false);

        $list = $qb->getQuery()->execute();

        $references = array();

        /** @var ReferenceInterface $reference */
        foreach ($list as $reference) {
            if (empty($references[$reference->getReferenceId()])) {
                $references[$reference->getReferenceId()] = $reference;
            }
        }

        return $references;
    }

    /**
     * @return array
     */
    public function findAllDeleted()
    {
        return $this->findBy(array('deleted' => true));
    }

    /**
     * @param $contentType
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
     * @param string|null $contentType
     * @param array|null $descriptionEntity
     * @param array|null $columns
     * @param string|null $search
     * @param string|null $siteId
     * @param array|null $order
     * @param int|null $skip
     * @param int|null $limit
     *
     * @return array
     */
    public function findByReferenceTypeForPaginateAndSearchAndSiteId(
        $referenceType = null,
        $descriptionEntity = null,
        $columns = null,
        $search = null,
        $siteId = null,
        $order = null,
        $skip = null,
        $limit = null)
    {
        $qa = $this->createAggregateQueryWithReferenceTypeFilter($referenceType);
        $qa = $this->generateFilterForSearch($qa, $descriptionEntity, $columns, $search);
        $qa->match($this->generateDeletedFilter());
        if (!is_null($siteId)) {
            $qa->match(array('$or' => array(array('siteId' => $siteId), array('linkedToSite' => false))));
        }

        $elementName = 'reference';

        $qa = $this->generateFilterSort($qa, $order, $descriptionEntity, $columns, $elementName);

        $qa = $this->generateSkipFilter($qa, $skip);
        $qa = $this->generateLimitFilter($qa, $limit);

        return $this->hydrateAggregateQuery($qa, $elementName);
    }

    /**
     * @param string|null $referenceType
     *
     * @return int
     */
    public function countByReferenceType($referenceType = null)
    {
        $qa = $this->createAggregateQueryWithReferenceTypeFilter($referenceType);
        $qa->match($this->generateDeletedFilter());
        $elementName = 'reference';

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string|null $contentType
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     *
     * @return int
     */
    public function countByReferenceTypeWithSearchFilter($referenceType = null, $descriptionEntity = null, $columns = null, $search = null)
    {
        $qa = $this->createAggregateQueryWithReferenceTypeFilter($referenceType);
        $qa = $this->generateFilterForSearch($qa, $descriptionEntity, $columns, $search);
        $qa->match($this->generateDeletedFilter());
        $elementName = 'reference';

        return $this->countDocumentAggregateQuery($qa, $elementName);
    }
}
