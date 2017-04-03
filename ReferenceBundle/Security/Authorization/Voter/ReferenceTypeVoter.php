<?php

namespace Itkg\ReferenceBundle\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Itkg\ReferenceModelBundle\Document\ReferenceType;
use Itkg\ReferenceInterface\Model\ReferenceTypeInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;

/*
 * Class ReferenceVoter
 *
 * Voter checking rights on reference management
 */
class ReferenceTypeVoter extends AbstractVoter
{
    /**
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supportSubject($subject)
    {
        return $subject instanceof ReferenceType || $subject === ReferenceTypeInterface::ENTITY_TYPE;
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
        return $this->hasRole($token, ContributionRoleInterface::DEVELOPER);
    }
}