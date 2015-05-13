<?php

namespace Itkg\ReferenceBundle\Manager;

use OpenOrchestra\Backoffice\Context\ContextManager;

/**
 * Class ReferenceManager
 */

class ReferenceManager
{
    protected $contextManager;

    /**
     * @param ContextManager $contextManager
     */
    public function __construct(ContextManager $contextManager)
    {
        $this->contextManager = $contextManager;
    }

    /**
     * @param ReferenceInterface $referenceSource
     * @param string           $language
     *
     * @return ReferenceInterface
     */
    public function createNewLanguageReference($referenceSource, $language)
    {
        $reference = clone $referenceSource;

        foreach ($referenceSource->getAttributes() as $attribute) {
            $newAttribute = clone $attribute;
            $reference->addAttribute($newAttribute);
        }

        $reference->setLanguage($language);

        return $reference;
    }
}
