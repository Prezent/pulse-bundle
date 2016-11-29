<?php

namespace Prezent\PulseBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Dispatch this event everytime you want to track something
 *
 * @see Event
 * @author Sander Marechal
 */
class PulseEvent extends Event
{
    /**
     * @const string Event name
     */
    const EVENT = 'prezent_pulse.event';

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $aggregates = [];

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * Constructor
     *
     * @param string $type
     * @param array $aggregates
     */
    public function __construct($type, array $aggregates = [], \DateTime $date = null)
    {
        $this->type = $type;
        $this->date = $date ?: new \DateTime();

        foreach ($aggregates as $key => $ids) {
            $this->addAggregate($key, $ids);
        }
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get aggregates
     *
     * @return array
     */
    public function getAggregates()
    {
        return $this->aggregates;
    }

    /**
     * Add an aggregate
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function addAggregate($key, $value)
    {
        if (!isset($this->aggregates[$key])) {
            $this->aggregates[$key] = [];
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $this->aggregates[$key] = array_merge(
            $this->aggregates[$key],
            array_values($value)
        );

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
