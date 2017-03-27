<?php

namespace Itkg\ReferenceBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ReferenceFormEvent
 */
class ReferenceFormEvent extends Event
{
    protected $builder;

    /**
     * @param FormBuilderInterface $builder
     */
    public function __construct(FormBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return FormBuilderInterface
     */
    public function getBuilder()
    {
        return $this->builder;
    }
}
