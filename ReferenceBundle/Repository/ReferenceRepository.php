<?php

namespace Itkg\ReferenceBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\ModelBundle\Repository\FieldAutoGenerableRepositoryInterface;
use Itkg\ReferenceInterface\Repository\ReferenceRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;

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
    protected function defaultQueryCriteria(Builder $qb, $referenceId, $language = null, $version = null)
    {
        if (is_null($language)) {
            $language = $this->currentSiteManager->getCurrentSiteDefaultLanguage();
        }
        $qb->field('referenceId')->equals($referenceId);
        $qb->field('language')->equals($language);
        $qb->field('deleted')->equals(false);

        return $qb;
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
