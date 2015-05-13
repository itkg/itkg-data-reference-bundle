<?php

namespace Itkg\ReferenceBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
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
            $qb->field('referenceType')->equals($referenceType);
        }

        $qb->field('deleted')->equals(false);

        $list = $qb->getQuery()->execute();

        $references = array();

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
}
