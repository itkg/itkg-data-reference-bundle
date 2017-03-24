<?php

namespace Itkg\ReferenceBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Class ReferenceTypeTypeSubscriber
 */
class ReferenceTypeTypeSubscriber implements EventSubscriberInterface
{
    /**
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        if (null !== $data->getReferenceTypeId()) {
            $field = $form->get('referenceTypeId');
            $config = $field->getConfig();
            $form->add($field->getName(),
                $config->getType()->getName(),
                array_merge($config->getOptions(), array('disabled' => true)));
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        );
    }
}
