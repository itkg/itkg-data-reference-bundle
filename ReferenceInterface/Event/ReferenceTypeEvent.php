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
     * @param ReferenceTypeInterface $referenceType
     */
    public function __construct(ReferenceTypeInterface $referenceType)
    {
        $this->referenceType = $referenceType;
    }

    /**
     * @return ContentTypeInterface
     */
    public function getReferenceType()
    {
        return $this->referenceType;
    }
}