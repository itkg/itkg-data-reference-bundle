<?php

namespace Itkg\ReferenceInterface\Model;

use OpenOrchestra\ModelInterface\Model\TimestampableInterface;
use OpenOrchestra\ModelInterface\Model\BlameableInterface;
use OpenOrchestra\ModelInterface\Model\SoftDeleteableInterface;
use OpenOrchestra\ModelInterface\Model\UseTrackableInterface;
use OpenOrchestra\ModelInterface\Model\HistorisableInterface;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;

/**
 * Interface ReferenceInterface
 */
interface ReferenceInterface extends ReadReferenceInterface, TimestampableInterface, BlameableInterface, SoftDeleteableInterface, UseTrackableInterface, HistorisableInterface
{
    const ENTITY_TYPE = 'reference';

    /**
     * @param ContentAttributeInterface $attribute
     */
    public function addAttribute(ContentAttributeInterface $attribute);

    /**
     * @param ContentAttributeInterface $attribute
     */
    public function removeAttribute(ContentAttributeInterface $attribute);

    /**
     * @param string $referenceId
     */
    public function setReferenceId($referenceId);

    /**
     * @return string
     */
    public function getReferenceId();

    /**
     * @param string $referenceTypeId
     */
    public function setReferenceTypeId($referenceTypeId);

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $language
     */
    public function setLanguage($language);

    /**
     * @param string $name
     */
    public function setName($name);
}
