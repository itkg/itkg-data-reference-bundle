<?php

namespace itkg\ReferenceInterface\Repository;

use itkg\ReferenceInterface\Model\ReferenceInterface;

/**
 * Interface ReferenceRepositoryInterface
 */
interface ReferenceRepositoryInterface
{
    /**
     * @return array list of news
     */
    public function findAll();

    /**
     * @param string $referenceId
     *
     * @return ReferenceInterface
     */
    public function findOneByReferenceId($referenceId);

    /**
     * @return array
     */
    public function findAllDeleted();
}
