<?php

namespace Itkg\ReferenceBundle\Repository;

use OpenOrchestra\Repository\AbstractAggregateRepository;
use Doctrine\ODM\MongoDB\Query\Builder;
use Itkg\ReferenceInterface\Model\ReferenceInterface;
use Itkg\ReferenceInterface\Repository\ReferenceRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;

/**
 * Class ReferenceRepository
 */
class ReferenceRepository extends AbstractAggregateRepository implements FieldAutoGenerableRepositoryInterface, ReferenceRepositoryInterface
{
    use PaginationTrait;

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
        return $this->findOneBy(array('referenceId' => $name)) !== null;
    }

    /**
     * @param string|null $language
     *
     * @return Builder
     */
    protected function createQueryBuilderWithLanguage($language = null)
    {
        $qb = $this->createQueryBuilder();
        if ($language != null) {
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
     * @return ReferenceInterface[]
     */
    public function findByReferenceTypeNotDeleted($referenceType = null)
    {
        $qb = $this->createQueryBuilder();
        $this->addReferenceTypeAndNotDeletedConstraint($qb, $referenceType);

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
     * @param PaginateFinderConfiguration $configuration
     * @param string                      $referenceType
     *
     * @return ReferenceInterface[]
     */
    public function findByReferenceTypeNotDeletedWithPagination(PaginateFinderConfiguration $configuration, $referenceType = null)
    {
        $stage = $this->createAggregationQuery();
        $this->generateFilterForPaginate($stage, $configuration);

        $constraints = [
            'deleted' => false,
        ];

        if ($referenceType !== null) {
            $constraints['referenceTypeId'] = $referenceType;
        }

        $stage->match($constraints);

        return $this->hydrateAggregateQuery($stage, null, 'getReferenceId');
    }

    /**
     * @param string $referenceType
     *
     * @return int
     */
    public function countByReferenceTypeNotDeleted($referenceType = null)
    {
        $qb = $this->createQueryBuilder();
        $this->addReferenceTypeAndNotDeletedConstraint($qb, $referenceType);

        return $qb->getQuery()->execute()->count();
    }

    /**
     * @return array
     */
    public function findAllDeleted()
    {
        return $this->findBy(array('deleted' => true));
    }

    /**
     * @param Builder $qb
     * @param string  $referenceType
     */
    private function addReferenceTypeAndNotDeletedConstraint(Builder $qb, $referenceType = null)
    {
        if ($referenceType) {
            $qb->field('referenceTypeId')->equals($referenceType);
        }

        $qb->field('deleted')->equals(false);
    }
}
