<?php

namespace Itkg\ReferenceBundle\Manager;

use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use OpenOrchestra\Backoffice\Util\UniqueIdGenerator;
use Itkg\ReferenceInterface\Model\ReferenceInterface;

/**
 * Class ReferenceManager
 */

class ReferenceManager
{
    protected $statusRepository;
    protected $contextManager;
    protected $referenceClass;
    
    /**
     * @param StatusRepositoryInterface $statusRepository
     * @param ContextManager            $contextManager
     * @param string                    $referenceClass
     * @param UniqueIdGenerator         $uniqueIdGenerator
     */
    public function __construct(
        StatusRepositoryInterface $statusRepository,
        ContextManager $contextManager,
        $referenceClass,
        UniqueIdGenerator $uniqueIdGenerator
    ) {
        $this->statusRepository = $statusRepository;
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
        $initialStatus = $this->statusRepository->findOneByInitial();

        $referenceClass = $this->referenceClass;
        /** @var ReferenceInterface $reference */
        $reference = new $referenceClass();
        $reference->setLanguage($language);
        $reference->setSiteId($this->contextManager->getCurrentSiteId());
        $reference->setReferenceType($referenceType);
        $reference->setStatus($initialStatus);
        $reference->setVersion($this->uniqueIdGenerator->generateUniqueId());

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
        $translationStatus = $this->statusRepository->findOneByTranslationState();
        $reference = $this->cloneReference($referenceSource);
        $reference->setLanguage($language);
        $reference->setStatus($translationStatus);
    
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
        $newReference = $this->setVersionName($newReference);

        return $newReference;
    }

    /**
     * Duplicate a reference
     *
     * @param ReferenceInterface $originalReference
     * @param string             $versionName
     *
     * @return ReferenceInterface
     */
    public function newVersionReference(ReferenceInterface $originalReference, $versionName = '')
    {
        $newReference = $this->cloneReference($originalReference);
        $newReference->setVersionName($versionName);
        if (empty($versionName)) {
            $newReference = $this->setVersionName($newReference);
        }
    
        return $newReference;
    }

    /**
     * @param ReferenceInterface $node
     *
     * @return ReferenceInterface
     */
    public function setVersionName(ReferenceInterface $node)
    {
        $date = new \DateTime("now");
        $versionName = $node->getName().'_'. $date->format("Y-m-d_H:i:s");
        $node->setVersionName($versionName);
    
        return $node;
    }

    /**
     * @param ReferenceInterface $reference
     *
     * @return ReferenceInterface
     */
    protected function cloneReference(ReferenceInterface $reference)
    {
        $status = $this->statusRepository->findOneByInitial();

        $newReference = clone $reference;
        $newReference->setStatus($status);
        $newReference->setVersion($this->uniqueIdGenerator->generateUniqueId());
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
