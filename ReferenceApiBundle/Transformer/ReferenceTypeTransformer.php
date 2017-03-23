<?php

namespace Itkg\ReferenceApiBundle\Transformer;

use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use Itkg\ReferenceInterface\Repository\ReferenceRepositoryInterface;
use Itkg\ReferenceInterface\Repository\ReferenceTypeRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Itkg\ReferenceInterface\Model\ReferenceTypeInterface;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class ReferenceTypeTransformer
 */
class ReferenceTypeTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $multiLanguagesChoiceManager;
    protected $referenceRepository;
    protected $referenceTypeRepository;

    /**
     * @param string                               $facadeClass
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param ReferenceRepositoryInterface         $referenceRepository
     * @param ReferenceTypeRepositoryInterface     $referenceTypeRepository
     * @param AuthorizationCheckerInterface        $authorizationChecker
     */
    public function __construct(
        $facadeClass,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        ReferenceRepositoryInterface $referenceRepository,
        ReferenceTypeRepositoryInterface $referenceTypeRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct($facadeClass, $authorizationChecker);
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->referenceRepository = $referenceRepository;
        $this->referenceTypeRepository = $referenceTypeRepository;

    }

    /**
     * @param ReferenceTypeInterface $referenceType
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($referenceType)
    {
        if (!$referenceType instanceof ReferenceTypeInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $referenceType->getId();
        $facade->referenceTypeId = $referenceType->getReferenceTypeId();
        $facade->name = $this->multiLanguagesChoiceManager->choose($referenceType->getNames());
        $facade->version = $referenceType->getVersion();
        $facade->linkedToSite = $referenceType->isLinkedToSite();
        $facade->definingVersionable = $referenceType->isDefiningVersionable();
        $facade->definingStatusable = $referenceType->isDefiningStatusable();
        $facade->defaultListable = $referenceType->getDefaultListable();

        if ($this->hasGroup(CMSGroupContext::AUTHORIZATIONS)) {
            $facade->addRight('can_delete', $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $referenceType) && 0 == $this->referenceRepository->countByReferenceType($referenceType->getReferenceTypeId()));
            $facade->addRight('can_duplicate', $this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, ReferenceTypeInterface::ENTITY_TYPE));
        }

        if ($this->hasGroup(CMSGroupContext::FIELD_TYPES)) {
            foreach ($referenceType->getFields() as $field) {
                $facade->addField($this->getTransformer('field_type')->transform($field));
            }
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null $source
     *
     * @return ReferenceTypeInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if (null !== $facade->referenceTypeId) {
            return $this->referenceTypeRepository->findOneByReferenceTypeIdInLastVersion($facade->referenceTypeId);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reference_type';
    }
}
