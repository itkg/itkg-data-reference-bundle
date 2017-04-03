<?php

namespace Itkg\ReferenceBundle\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\Authorization\Voter\AbstractVoter;
use Itkg\ReferenceModelBundle\Document\Reference;
use Itkg\ReferenceBundle\Security\ReferenceRoleInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Itkg\ReferenceInterface\Model\ReferenceInterface;

/*
 * Class ReferenceVoter
 *
 * Voter checking rights on reference management
 */
class ReferenceVoter extends AbstractVoter
{
    /**
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supportSubject($subject)
    {
        return $subject instanceof Reference || $subject === ReferenceInterface::ENTITY_TYPE;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        return $this->hasRole($token, ReferenceRoleInterface::REFERENCE_ADMIN) || $this->isSuperAdmin($token);
    }
}