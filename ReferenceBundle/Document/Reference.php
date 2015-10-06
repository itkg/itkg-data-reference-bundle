<?php

namespace Itkg\ReferenceBundle\Document;

use Itkg\ReferenceInterface\Model\ReferenceInterface;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use OpenOrchestra\MongoTrait\Keywordable;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
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
     * @ORCHESTRA\Search(key="referenceId", field="referenceId", type="string")
     */
    protected $referenceId;

    /**
     * @var string $referenceType
     *
     * @ODM\Field(type="string")
     */
    protected $referenceTypeId;

    /**
     * @var string $name
     *
     * @ODM\Field(type="string")
     * @ORCHESTRA\Search(key="name", field="name", type="string")
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
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\ContentAttributeInterface", strategy="set")
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
     * Clone method
     */
    public function __clone()
    {
        $this->id = null;
        $this->initializeCollections();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
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
        return $this->attributes->get($name);
    }

    /**
     * @param ContentAttributeInterface $attribute
     */
    public function addAttribute(ContentAttributeInterface $attribute)
    {
        $this->attributes->set($attribute->getName(), $attribute);
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
     * @param $referenceTypeId
     */
    public function setReferenceTypeId($referenceTypeId)
    {
        $this->referenceTypeId = $referenceTypeId;
    }

    /**
     * @return string
     */
    public function getReferenceTypeId()
    {
        return $this->referenceTypeId;
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
