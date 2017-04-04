<?php

namespace Itkg\ReferenceInterface\Model;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ModelInterface\Model\KeywordableInterface;

/**
 * Interface ReadReferenceInterface
 */
interface ReadReferenceInterface extends KeywordableInterface
{
    /**
     * @return ArrayCollection
     */
    public function getAttributes();

    /**
     * @param string $name
     *
     * @return ReadContentAttributeInterface|null
     */
    public function getAttributeByName($name);

    /**
     * @return string
     */
    public function getLanguage();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getReferenceType();
}
