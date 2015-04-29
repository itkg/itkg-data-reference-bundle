<?php
namespace Itkg\ReferenceApiBundle\EventSubscriber;

use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use Itkg\ReferenceApiBundle\Repository\ReferenceTypeRepository;
use Symfony\Component\Form\FormEvent;

/**
 * Class ReferenceTypeSubscriber
 */
class ReferenceTypeSubscriber extends AbstractBlockContentTypeSubscriber
{
    protected $translationChoiceManager;
    protected $referenceTypeRepository;
    protected $contentAttributClass;

    /**
     * @param ReferenceTypeRepositoryInterface $referenceTypeRepository
     * @param string                         $contentAttributClass
     * @param TranslationChoiceManager       $translationChoiceManager
     */
    public function __construct(
        ReferenceTypeRepositoryInterface $referenceTypeRepository,
        $contentAttributClass,
        TranslationChoiceManager $translationChoiceManager
    )
    {
        $this->referenceTypeRepository = $referenceTypeRepository;
        $this->contentAttributClass = $contentAttributClass;
        $this->translationChoiceManager = $translationChoiceManager;
    }
}
