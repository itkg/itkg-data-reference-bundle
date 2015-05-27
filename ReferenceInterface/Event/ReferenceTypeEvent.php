<?php

namespace Itkg\ReferenceInterface\Event;

use Itkg\ReferenceInterface\Model\ReferenceTypeInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ReferenceTypeEvent
 */
class ReferenceTypeEvent extends Event
{
    protected $referenceType;

    /**
     * @param ReferenceTypenterface $referenceType
     */
    public function __construct(ReferenceTypeInterface $referenceType)
    {
        $this->referenceType = $referenceType;
    }

    /**
     * @return ReferenceTypeInterface
     */
    public function getReferenceType()
    {
        return $this->referenceType;
    }
}
