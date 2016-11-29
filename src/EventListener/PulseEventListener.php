<?php

namespace Prezent\PulseBundle\EventListener;

use Prezent\PulseBundle\Event\PulseEvent;
use Prezent\PulseBundle\Repository\EventRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Store and process pulse events
 *
 * @see EventSubscriberInterface
 * @author Sander Marechal
 */
class PulseEventListener implements EventSubscriberInterface
{
    /**
     * @var EventRepository
     */
    private $repository;

    /**
     * Constructor
     *
     * @param EventRepository $repository
     */
    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle a PulseEvent
     *
     * @param PulseEvent $event
     * @return void
     */
    public function onEvent(PulseEvent $event)
    {
        $this->repository->append($event);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PulseEvent::EVENT => ['onEvent'],
        ];
    }
}
