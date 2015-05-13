<?php

namespace Itkg\ReferenceInterface\Event;

use Itkg\ReferenceInterface\Model\ReferenceInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ReferenceEvent
 */
class ReferenceEvent extends Event
{
    protected $reference;

    /**
     * @param ReferenceInterface $reference
     */
    public function __construct(ReferenceInterface $reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return referenceInterface
     */
    public function getReference()
    {
        return $this->reference;
    }
}
