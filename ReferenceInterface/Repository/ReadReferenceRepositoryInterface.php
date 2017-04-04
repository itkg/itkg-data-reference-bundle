<?php

namespace Itkg\ReferenceInterface\Repository;

/**
 * Interface ReferenceRepositoryInterface
 */
interface ReadReferenceRepositoryInterface
{
    const CHOICE_AND = 'choice_and';
    const CHOICE_OR = 'choice_or';

    /**
     * @param string $referenceId
     *
     * @return ReferenceInterface
     */
    public function findOneByReferenceId($referenceId);

    /**
     * @param string      $language
     * @param string      $referenceType
     * @param string      $choiceType
     * @param string|null $condition
     *
     * @return array
     */
    public function findByReferenceTypeAndCondition($language, $referenceType = '', $choiceType = self::CHOICE_AND, $condition = null);
}
