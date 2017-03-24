<?php

namespace Itkg\ReferenceApiBundle\Transformer;

use Itkg\ReferenceInterface\Model\ReferenceInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Itkg\ReferenceInterface\Repository\ReferenceRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ApiBundle\Context\CMSGroupContext;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class ReferenceTransformer
 */
class ReferenceTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $statusRepository;
    protected $referenceRepository;
    protected $contextManager;

    /**
     * @param string                         $facadeClass
     * @param StatusRepositoryInterface      $statusRepository
     * @param ReferenceRepositoryInterface     $referenceRepository,
     * @param AuthorizationCheckerInterface  $authorizationChecker
     * @param CurrentSiteIdInterface         $contextManager
     */
    public function __construct(
        $facadeClass,
        StatusRepositoryInterface $statusRepository,
        ReferenceRepositoryInterface $referenceRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        CurrentSiteIdInterface $contextManager
    ) {
        $this->statusRepository = $statusRepository;
        $this->referenceRepository = $referenceRepository;
        $this->contextManager = $contextManager;
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
        $facade->version = $reference->getVersion();
        $facade->versionName = $reference->getVersionName();
        $facade->language = $reference->getLanguage();
        $facade->status = $this->getTransformer('status')->transform($reference->getStatus());
        $facade->statusLabel = $reference->getStatus()->getLabel($this->contextManager->getCurrentLocale());
        $facade->createdAt = $reference->getCreatedAt();
        $facade->updatedAt = $reference->getUpdatedAt();
        $facade->createdBy = $reference->getCreatedBy();
        $facade->updatedBy = $reference->getUpdatedBy();
        $facade->deleted = $reference->isDeleted();
        $facade->linkedToSite = $reference->isLinkedToSite();
        $facade->used = $reference->isUsed();

        foreach ($reference->getAttributes() as $attribute) {
            $referenceAttribute = $this->getTransformer('content_attribute')->transform($attribute);
            $facade->addAttribute($referenceAttribute);
        }
        if ($this->hasGroup(CMSGroupContext::AUTHORIZATIONS)) {
            $facade->addRight('can_delete',(
                false === $this->referenceRepository->hasReferenceIdWithoutAutoUnpublishToState($reference->getReferenceId()) &&
                $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $reference)
            ));
            $facade->addRight('can_duplicate', $this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, ReferenceInterface::ENTITY_TYPE));
        }

        if ($this->hasGroup(CMSGroupContext::AUTHORIZATIONS_DELETE_VERSION)) {
            $facade->addRight('can_delete_version', $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $reference) && !$reference->getStatus()->isPublishedState());
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param ReferenceInterface|null         $source
     *
     * @return mixed
     * @throws StatusChangeNotGrantedHttpException
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if ($source instanceof ReferenceInterface &&
            null !== $facade->status &&
            null !== $facade->status->id &&
            $source->getStatus()->getId() !== $facade->status->id
        ) {
            $status = $this->statusRepository->find($facade->status->id);
            if ($status instanceof StatusInterface) {
                $source->setStatus($status);
            }
        }

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
