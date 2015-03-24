<?php

namespace itkg\ReferenceBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use OpenOrchestra\ModelBundle\Mapping\Annotations as ORCHESTRA;
//use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;
use itkg\ReferenceInterface\Model\ReferenceInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use OpenOrchestra\ModeInterface\MongoTrait\Keywordable;

/**
 * Description of Reference
 *
 * @ODM\Document(
 *   collection="reference",
 *   repositoryClass="itkg\ReferenceBundle\Repository\ReferenceRepository"
 * )
 * @ORCHESTRA\Document(
 *   generatedField="referenceId",
 *   sourceField="name",
 *   serviceName="itkg_model.repository.reference",
 * )
 */
class Reference implements ReferenceInterface
{
    use BlameableDocument;
    use TimestampableDocument;
    use Keywordable;

    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var int $referenceId
     *
     * @ODM\Field(type="string")
     */
    protected $referenceId;

    /**
     * @var string $referenceType
     *
     * @ODM\Field(type="string")
     */
    protected $referenceType;

    /**
     * @var string $name
     *
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @var int $version
     *
     * @ODM\Field(type="int")
     */
    protected $version = 1;

    /**
     * @var int $referenceTypeVersion
     *
     * @ODM\Field(type="int")
     */
    protected $referenceTypeVersion;

    /**
     * @var string $language
     *
     * @ODM\Field(type="string")
     */
    protected $language;

    /**
     * @var StatusInterface $status
     *
     * @ODM\EmbedOne(targetDocument="EmbedStatus")
     */
    protected $status;

    /**
     * @var boolean
     *
     * @ODM\Field(type="boolean")
     */
    protected $deleted = false;

    /**
     * @var ArrayCollection
     *
     * @ODM\EmbedMany(targetDocument="referenceAttribute")
     */
    protected $attributes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initializeCollections();
    }

    /**
     * @return ArrayCollection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     *
     * @return referenceAttributeInterface|null
     */
    public function getAttributeByName($name)
    {
        foreach ($this->attributes as $attribute) {
            if ($name == $attribute->getName()) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * @param referenceAttributeInterface $attribute
     */
    public function addAttribute(ReferenceAttributeInterface $attribute)
    {
        $this->attributes->add($attribute);
    }

    /**
     * @param ReferenceAttributeInterface $attribute
     */
    public function removeAttribute(ReferenceAttributeInterface $attribute)
    {
        $this->attributes->removeElement($attribute);
    }

    /**
     * @param string $referenceId
     */
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
    }

    /**
     * @return string
     */
    public function getReferenceId()
    {
        return $this->referenceId;
    }

    /**
     * @param string $referenceType
     */
    public function setReferenceType($referenceType)
    {
        $this->referenceType = $referenceType;
    }

    /**
     * @return string
     */
    public function getReferenceType()
    {
        return $this->referenceType;
    }

    /**
     * @param int $referenceTypeVersion
     */
    public function setReferenceTypeVersion($referenceTypeVersion)
    {
        $this->referenceTypeVersion = $referenceTypeVersion;
    }

    /**
     * @return int
     */
    public function getReferenceTypeVersion()
    {
        return $this->referenceTypeVersion;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set status
     *
     * @param StatusInterface|null $status
     */
    public function setStatus(StatusInterface $status = null)
    {
        $this->status = null;
        if ($status instanceof StatusInterface) {
            $this->status = EmbedStatus::createFromStatus($status);
        }
    }

    /**
     * Get status
     *
     * @return StatusInterface $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Clone method
     */
    public function __clone()
    {
        $this->id = null;
        $this->initializeCollections();
    }

    /**
     * initialize collections
     */
    protected function initializeCollections()
    {
        $this->attributes = new ArrayCollection();
        $this->keywords = new ArrayCollection();
    }
}
