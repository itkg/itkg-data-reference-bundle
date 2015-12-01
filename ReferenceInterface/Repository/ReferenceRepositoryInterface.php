<?php

namespace Itkg\ReferenceInterface\Repository;

use Itkg\ReferenceInterface\Model\ReferenceInterface;

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
     * @param string $id
     *
     * @return ReferenceInterface
     */
    public function find($id);

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
