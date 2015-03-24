<?php

namespace itkg\ReferenceBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelBundle\Repository\FieldAutoGenerableRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\ReferenceInterface;
use itkg\ReferenceInterface\Repository\ReferenceRepositoryInterface;
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
        if (is_null($version)) {
            $qb->sort('version', 'desc');
        } else {
            $qb->field('version')->equals((int) $version);
        }

        return $qb;
    }

    /**
     * Get all reference if the referenceType is "news"
     *
     * @return array list of news
     */
    public function findAllNews()
    {
        $criteria = array(
            'referenceType'=> "news",
            'status'=> "published"
        );

        return $this->findBy($criteria);
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
     * @param string $referenceId
     *
     * @return ReferenceInterface
     */
    public function findOneByReferenceId($referenceId)
    {
        return $this->findOneBy(array('referenceId' => $referenceId));
    }

    /**
     * @param string $referenceType
     * @param string $choiceType
     * @param string $keywords
     *
     * @return array
     */
    public function findByReferenceTypeAndChoiceTypeAndKeywords($referenceType = '', $choiceType = self::CHOICE_AND, $keywords = null)
    {
        $qb = $this->getQueryFindByReferenceTypeAndChoiceTypeAndKeywords($referenceType, $choiceType, $keywords);

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $referenceType
     * @param string $choiceType
     * @param string $keywords
     *
     * @return array
     */
    public function findByReferenceTypeAndChoiceTypeAndKeywordsNotHydrated($referenceType = '', $choiceType = self::CHOICE_AND, $keywords = null)
    {
        $qb = $this->getQueryFindByReferenceTypeAndChoiceTypeAndKeywords($referenceType, $choiceType, $keywords);

        return $qb->hydrate(false)->getQuery()->execute();
    }

    /**
     * @param string      $referenceId
     * @param string|null $language
     *
     * @return ReferenceInterface|null
     */
    public function findOneByReferenceIdAndLanguage($referenceId, $language = null)
    {
        return $this->findOneByReferenceIdAndLanguageAndVersion($referenceId, $language, null);
    }

    /**
     * @param string      $referenceId
     * @param string|null $language
     *
     * @return array
     */
    public function findByReferenceIdAndLanguage($referenceId, $language = null)
    {
        $qb = $this->createQueryBuilder('c');
        $qb = $this->defaultQueryCriteria($qb, $referenceId, $language, null);

        return $qb->getQuery()->execute();
    }

    /**
     * @param string      $referenceId
     * @param string|null $language
     * @param int|null    $version
     *
     * @return ReferenceInterface|null
     */
    public function findOneByReferenceIdAndLanguageAndVersion($referenceId, $language = null, $version = null)
    {
        $qb = $this->createQueryBuilder('c');
        $qb = $this->defaultQueryCriteria($qb, $referenceId, $language, $version);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @param string $referenceType
     *
     * @return array
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function findByReferenceTypeInLastVersion($referenceType = null)
    {
        $qb = $this->createQueryBuilder('c');
        if ($referenceType) {
            $qb->field('referenceType')->equals($referenceType);
        }
        $qb->field('deleted')->equals(false);
        $qb->sort('version', 'desc');

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
        return parent::findBy(array('deleted' => true));
    }

    /**
     * @param $referenceType
     * @param $choiceType
     * @param $keywords
     * @return Builder
     */
    protected function getQueryFindByReferenceTypeAndChoiceTypeAndKeywords($referenceType, $choiceType, $keywords)
    {
        $qb = $this->createQueryBuilder('c');

        $addMethod = 'addAnd';
        if ($choiceType == self::CHOICE_OR) {
            $addMethod = 'addOr';
        }

        if (!is_null($keywords)) {
            $qb->$addMethod($qb->expr()->field('keywords.label')->in(explode(',', $keywords)));
        }
        if ('' !== $referenceType) {
            $qb->$addMethod($qb->expr()->field('referenceType')->equals($referenceType));
            return $qb;
        }
        return $qb;
    }
}
