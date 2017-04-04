<?php

namespace Itkg\ReferenceApiBundle\Transformer;

use Itkg\ReferenceInterface\Model\ReferenceInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use Itkg\ReferenceInterface\Repository\ReferenceRepositoryInterface;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ApiBundle\Context\CMSGroupContext;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class ReferenceTransformer
 */
class ReferenceTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $referenceRepository;

    /**
     * @param string                        $facadeClass
     * @param ReferenceRepositoryInterface  $referenceRepository,
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        $facadeClass,
        ReferenceRepositoryInterface $referenceRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->referenceRepository = $referenceRepository;
        parent::__construct($facadeClass, $authorizationChecker);
    }

    /**
     * @param ReferenceInterface $reference
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($reference)
    {
        if (!$reference instanceof ReferenceInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();
        $facade->id = $reference->getId();
        $facade->referenceId = $reference->getReferenceId();
        $facade->referenceType = $reference->getReferenceType();
        $facade->name = $reference->getName();
        $facade->language = $reference->getLanguage();
        $facade->createdAt = $reference->getCreatedAt();
        $facade->updatedAt = $reference->getUpdatedAt();
        $facade->createdBy = $reference->getCreatedBy();
        $facade->updatedBy = $reference->getUpdatedBy();
        $facade->deleted = $reference->isDeleted();
        $facade->used = $reference->isUsed();

        foreach ($reference->getAttributes() as $attribute) {
            $referenceAttribute = $this->getTransformer('content_attribute')->transform($attribute);
            $facade->addAttribute($referenceAttribute);
        }
        if ($this->hasGroup(CMSGroupContext::AUTHORIZATIONS)) {
            $facade->addRight('can_delete', $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $reference));
            $facade->addRight('can_duplicate', $this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, ReferenceInterface::ENTITY_TYPE));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface         $facade
     * @param ReferenceInterface|null $source
     *
     * @return mixed
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if (null !== $facade->id) {
            return $this->referenceRepository->findById($facade->id);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reference';
    }
}
