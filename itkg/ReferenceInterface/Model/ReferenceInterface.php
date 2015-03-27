<?php


namespace itkg\ReferenceInterface\Model;

use Doctrine\Common\Collections\ArrayCollection;
//use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\TimestampableInterface;
use OpenOrchestra\ModelInterface\Model\BlameableInterface;
use OpenOrchestra\ModelInterface\Model\KeywordableInterface;

/**
 * Interface ReferenceInterface
 */
interface ReferenceInterface extends TimestampableInterface, BlameableInterface, KeywordableInterface
{
    /**
     * @return ArrayCollection
     */
    //public function getAttributes();

    /**
     * @param string $name
     *
     * @return ReferenceAttributeInterface|null
     */
    //public function getAttributeByName($name);

    /**
     * @param ReferenceAttributeInterface $attribute
     */
    //public function addAttribute(ReferenceAttributeInterface $attribute);

    /**
     * @param ReferenceAttributeInterface $attribute
     */
    //public function removeAttribute(ReferenceAttributeInterface $attribute);

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
     * @param int $referenceTypeVersion
     */
    public function setReferenceTypeVersion($referenceTypeVersion);

    /**
     * @return int
     */
    public function getReferenceTypeVersion();

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

    /**
     * @param int $version
     */
    public function setVersion($version);

    /**
     * @return int
     */
    public function getVersion();
}
