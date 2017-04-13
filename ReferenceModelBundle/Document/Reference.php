<?php

namespace Itkg\ReferenceModelBundle\Document;

use Itkg\ReferenceInterface\Model\ReferenceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use OpenOrchestra\MongoTrait\SoftDeleteable;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;
use OpenOrchestra\ModelInterface\Model\ReadContentAttributeInterface;
use OpenOrchestra\MongoTrait\Keywordable;
use OpenOrchestra\MongoTrait\UseTrackable;
use OpenOrchestra\MongoTrait\Historisable;

/**
 * Description of Reference
 *
 * @ODM\Document(
 *   collection="reference",
 *   repositoryClass="Itkg\ReferenceModelBundle\Repository\ReferenceRepository"
 * )
 * @ODM\Indexes({
 *  @ODM\Index(keys={"referenceId"="asc"}),
 *  @ODM\Index(keys={"language"="asc", "deleted"="asc", "referenceTypeId"="asc", "keywords.label"="asc"}),
 *  @ODM\Index(keys={"language"="asc", "deleted"="asc", "keywords.label"="asc"}),
 *  @ODM\Index(keys={"language"="asc", "deleted"="asc", "referenceTypeId"="asc"}),
 *  @ODM\Index(keys={"language"="asc", "deleted"="asc"}),
 *  @ODM\Index(keys={"keywords"="asc"})
 * })
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
    use SoftDeleteable;
    use UseTrackable;
    use Historisable;

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
     * @var string $referenceTypeId
     *
     * @ODM\Field(type="string")
     */
    protected $referenceTypeId;

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
     * @return ArrayCollection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     *
     * @return ReadContentAttributeInterface|null
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
        $this->attributes->remove($attribute->getName());
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
     * @param string $referenceTypeId
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
     * Clone method
     */
    public function __clone()
    {
        $this->id = null;
        $this->useReferences = array();
        $this->initializeCollections();
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * initialize collections
     */
    protected function initializeCollections()
    {
        $this->attributes = new ArrayCollection();
        $this->initializeKeywords();
        $this->initializeHistories();
    }
}
