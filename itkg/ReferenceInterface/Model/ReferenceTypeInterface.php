<?php

namespace itkg\ReferenceInterface\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface ReferenceInterface
 */
interface ReferenceInterface extends FieldTypeContainerInterface, StatusableInterface, TranslatedValueContainerInterface, BlameableInterface, TimestampableInterface
{
    /**
     * @param string $referenceTypeId
     */
    public function setReferenceTypeId($referenceTypeId);

    /**
     * @return string
     */
    public function getReferenceTypeId();

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted);

    /**
     * @return boolean
     */
    public function getDeleted();

    /**
     * @param FieldTypeInterface $fields
     */
    public function setFields(FieldTypeInterface $fields);

    /**
     * @return string
     */
    public function getId();

    /**
     * @param TranslatedValueInterface $name
     */
    public function addName(TranslatedValueInterface $name);

    /**
     * @param TranslatedValueInterface $name
     */
    public function removeName(TranslatedValueInterface $name);

    /**
     * @param string $language
     *
     * @return string
     */
    public function getName($language = 'en');

    /**
     * @return ArrayCollection
     */
    public function getNames();

    /**
     * @param int $version
     */
    public function setVersion($version);

    /**
     * @return int
     */
    public function getVersion();
}
