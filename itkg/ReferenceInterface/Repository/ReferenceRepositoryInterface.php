<?php

namespace itkg\ReferenceInterface\Repository;

use itkg\ReferenceInterface\Model\ReferenceInterface;

/**
 * Interface ReferenceRepositoryInterface
 */
interface ReferenceRepositoryInterface
{
    const CHOICE_AND = 'choice_and';
    const CHOICE_OR = 'choice_or';

    /**
     * Get all reference if the referenceType is "news"
     *
     * @return array list of news
     */
    public function findAllNews();

    /**
     * @return array list of news
     */
    public function findAll();

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function testUnicityInContext($name);

    /**
     * @param string $referenceId
     *
     * @return ReferenceInterface
     */
    public function findOneByReferenceId($referenceId);

    /**
     * @param string $referenceType
     * @param string $choiceType
     * @param string $keywords
     *
     * @return array
     */
    public function findByReferenceTypeAndChoiceTypeAndKeywords($referenceType = '', $choiceType = self::CHOICE_AND, $keywords = null);

    /**
     * @param string $referenceId
     * @param string $language
     *
     * @return ReferenceInterface|null
     */
    public function findOneByReferenceIdAndLanguage($referenceId, $language);

    /**
     * @param string      $referenceId
     * @param string|null $language
     *
     * @return array
     */
    public function findByReferenceIdAndLanguage($referenceId, $language = null);

    /**
     * @param string      $referenceId
     * @param string|null $language
     * @param int|null    $version
     *
     * @return ReferenceInterface|null
     */
    public function findOneByReferenceIdAndLanguageAndVersion($referenceId, $language = null, $version = null);

    /**
     * @param string $referenceType
     *
     * @return array
     */
    public function findByReferenceTypeInLastVersion($referenceType = null);

    /**
     * @return array
     */
    public function findAllDeleted();
}
