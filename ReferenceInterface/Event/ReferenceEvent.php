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
     * @param ContentInterface $content
     */
    public function __construct(ReferenceInterface $reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return ContentInterface
     */
    public function getContent()
    {
        return $this->reference;
    }
}
