<?php


namespace Itkg\ReferenceInterface\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ReferenceDeleteEvent
 */
class ReferenceDeleteEvent extends Event
{
    protected $referenceId;

    /**
     * @param string $referenceId
     */
    public function __construct($referenceId)
    {
        $this->referenceId = $referenceId;
    }

    /**
     * @return string
     */
    public function getReferenceId()
    {
        return $this->referenceId;
    }
}
