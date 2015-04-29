<?php

namespace Itkg\ReferenceBundle\Document;

use Gedmo\Timestampable\TimestampableDocumentTest;
use Itkg\ReferenceInterface\Model\ReferenceInterface;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use OpenOrchestra\ModelInterface\MongoTrait\Keywordable;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Mapping\Annotations as ORCHESTRA;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of Reference
 *
 * @ODM\Document(
 *   collection="reference",
 *   repositoryClass="Itkg\ReferenceBundle\Repository\ReferenceRepository"
 * )
 * @ORCHESTRA\Document(
 *   generatedField="referenceId",
 *   sourceField="name",
 *   serviceName="itkg_reference.repository.reference",
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
     * @var string $language
     *
     * @ODM\Field(type="string")
     */
    protected $language;

    /**
     * @var boolean
     *
     * @ODM\Field(type="boolean")
     */
    protected $deleted = false;

    /**
     * @var ArrayCollection
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\ContentAttributeInterface")
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
     * @return ContentAttributeInterface|null
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
     * @param ContentAttributeInterface $attribute
     */
    public function addAttribute(ContentAttributeInterface $attribute)
    {
        $this->attributes->add($attribute);
    }

    /**
     * @param ContentAttributeInterface $attribute
     */
    public function removeAttribute(ContentAttributeInterface $attribute)
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
     * initialize collections
     */
    protected function initializeCollections()
    {
        $this->attributes = new ArrayCollection();
        $this->keywords = new ArrayCollection();
    }
}
