<?php

namespace Itkg\ReferenceBundle\Manager;

use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\Backoffice\Util\UniqueIdGenerator;
use Itkg\ReferenceInterface\Model\ReferenceInterface;

/**
 * Class ReferenceManager
 */

class ReferenceManager
{
    protected $contextManager;
    protected $referenceClass;

    /**
     * @param ContextManager            $contextManager
     * @param string                    $referenceClass
     * @param UniqueIdGenerator         $uniqueIdGenerator
     */
    public function __construct(
        ContextManager $contextManager,
        $referenceClass,
        UniqueIdGenerator $uniqueIdGenerator
    ) {
        $this->contextManager = $contextManager;
        $this->referenceClass = $referenceClass;
        $this->uniqueIdGenerator = $uniqueIdGenerator;
    }

    /**
     * @param string  $referenceType
     * @param string  $language
     *
     * @return ReferenceInterface
     */
    public function initializeNewReference($referenceType, $language)
    {
        $referenceClass = $this->referenceClass;
        /** @var ReferenceInterface $reference */
        $reference = new $referenceClass();
        $reference->setLanguage($language);
        $reference->setSiteId($this->contextManager->getCurrentSiteId());
        $reference->setReferenceType($referenceType);

        return $reference;
    }

    /**
     * @param ReferenceInterface $referenceSource
     * @param string             $language
     *
     * @return ReferenceInterface
     */
    public function createNewLanguageReference($referenceSource, $language)
    {
        $reference = $this->cloneReference($referenceSource);
        $reference->setLanguage($language);

        return $reference;
    }

    /**
     * Duplicate a reference
     *
     * @param ReferenceInterface $reference
     * @param string|null        $referenceId
     *
     * @return ReferenceInterface
     */
    public function duplicateReference(ReferenceInterface $reference, $referenceId = null)
    {
        $newReference = $this->cloneReference($reference);
        $newReference->setReferenceId($referenceId);
        $newReference->setName($this->duplicateLabel($reference->getName()));

        return $newReference;
    }

    /**
     * @param ReferenceInterface $reference
     *
     * @return ReferenceInterface
     */
    protected function cloneReference(ReferenceInterface $reference)
    {
        $newReference = clone $reference;
        foreach ($reference->getKeywords() as $keyword) {
            $newReference->addKeyword($keyword);
        }
        foreach ($reference->getAttributes() as $attribute) {
            $newAttribute = clone $attribute;
            $newReference->addAttribute($newAttribute);
        }

        return $newReference;
    }

    /**
     * @param string $label
     *
     * @return string
     */
    protected function duplicateLabel($label)
    {
        $patternNameVersion = '/.*_([0-9]+$)/';
        if (0 !== preg_match_all($patternNameVersion, $label, $matches)) {
            $version = (int) $matches[1][0] + 1;
            return preg_replace('/[0-9]+$/', $version, $label);
        }

        return $label . '_2';
    }
}
