<?php

namespace Itkg\ReferenceBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\ModelBundle\Repository\FieldAutoGenerableRepositoryInterface;
use Itkg\ReferenceInterface\Repository\ReferenceRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Class ReferenceRepository
 */
class ReferenceRepository extends DocumentRepository implements FieldAutoGenerableRepositoryInterface, ReferenceRepositoryInterface
{
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
    public function testUnicityInContext($name)
    {
        return $this->findOneByName($name) !== null;
    }

    /**
     * @param string      $referenceId
     * @param string|null $language
     * @param int|null    $version
     *
     * @return Builder
     */
    protected function defaultQueryCriteria($language = null)
    {
        $qb = $this->createQueryBuilder('r');
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
        $qb = $this->defaultQueryCriteria($language);

        $qb = $qb->field('referenceId')->equals($referenceId);

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
     * @return array
     */
    public function findAllDeleted()
    {
        return $this->findBy(array('deleted' => true));
    }
}
