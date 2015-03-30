<?php

namespace itkg\ReferenceInterface\Model;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ModelInterface\Model\TimestampableInterface;
use OpenOrchestra\ModelInterface\Model\BlameableInterface;
use OpenOrchestra\ModelInterface\Model\KeywordableInterface;
use OpenOrchestra\ModelBundle\Document\ContentAttribute;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;

/**
 * Interface ReferenceInterface
 */
interface ReferenceInterface extends TimestampableInterface, BlameableInterface, KeywordableInterface
{
    /**
     * @return ArrayCollection
     */
    public function getAttributes();

    /**
     * @param string $name
     *
     * @return ContentAttributeInterface|null
     */
    public function getAttributeByName($name);

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
     * @param string $referenceType
     */
    public function setReferenceType($referenceType);

    /**
     * @return string
     */
    public function getReferenceType();

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted);

    /**
     * @return boolean
     */
    public function getDeleted();

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $language
     */
    public function setLanguage($language);

    /**
     * @return string
     */
    public function getLanguage();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

}
