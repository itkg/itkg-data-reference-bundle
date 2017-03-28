<?php

namespace Itkg\ReferenceInterface\Model;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ModelInterface\Model\FieldTypeContainerInterface;
use OpenOrchestra\ModelInterface\Model\BlameableInterface;
use OpenOrchestra\ModelInterface\Model\TimestampableInterface;
use OpenOrchestra\ModelInterface\Model\VersionableInterface;
use OpenOrchestra\ModelInterface\Model\SoftDeleteableInterface;

/**
 * Interface ReferenceTypeInterface
 */
interface ReferenceTypeInterface extends FieldTypeContainerInterface, BlameableInterface, TimestampableInterface, VersionableInterface, SoftDeleteableInterface
{
    const ENTITY_TYPE = 'reference_type';

    /**
     * @param string $referenceTypeId
     */
    public function setReferenceTypeId($referenceTypeId);

    /**
     * @return string
     */
    public function getReferenceTypeId();

    /**
     * @param string $template
     */
    public function setTemplate($template);

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @param Collection $fields
     */
    public function setFields(Collection $fields);

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $language
     * @param string $name
     */
    public function addName($language, $name);

    /**
     * @param string $language
     */
    public function removeName($language);

    /**
     * @param string $language
     *
     * @return string
     */
    public function getName($language);

    /**
     * @return array
     */
    public function getNames();

    /**
     * @param array $names
     */
    public function setNames(array $names);

    /**
     * @return array
     */
    public function getDefaultListable();

    /**
     * @param string  $name
     * @param boolean $value
     */
    public function addDefaultListable($name, $value);

    /**
     * @param string $name
     */
    public function removeDefaultListable($name);

    /**
     * @param array $defaultListable
     */
    public function setDefaultListable(array $defaultListable);
}
