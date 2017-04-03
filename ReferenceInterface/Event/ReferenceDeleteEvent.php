<?php


namespace Itkg\ReferenceInterface\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ReferenceDeleteEvent
 */
class ReferenceDeleteEvent extends Event
{
    protected $referenceId;
    protected $siteId;

    /**
     * @param string $referenceId
     * @param string $siteId
     */
    public function __construct($referenceId, $siteId)
    {
        $this->referenceId = $referenceId;
        $this->siteId = $siteId;
    }

    /**
     * @return string
     */
    public function getReferenceId()
    {
        return $this->referenceId;
    }

    /**
     * @return string
     */
    public function getSiteId()
    {
        return $this->siteId;
    }
}
