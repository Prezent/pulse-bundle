<?php

namespace Prezent\PulseBundle\Query;

/**
 * Aggregate query
 *
 * @author Sander Marechal
 */
class Query
{
    /**
     * @var array
     */
    private $types = [];

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var array
     */
    private $filters = [];

    /**
     * Get types
     *
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set types
     *
     * @param array $types
     * @return self
     */
    public function setTypes(array $types)
    {
        $this->types = $types;
        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return self
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return self
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * Get filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Add an aggregate filter
     *
     * @param string $key
     * @param array|mixed $ids
     * @return self
     */
    public function addFilter($key, $ids = null)
    {
        if (!isset($this->filters[$key])) {
            $this->filters[$key] = [];
        }

        if ($ids === null) {
            $ids = [];
        }

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $this->filters[$key] = array_merge($ids, $this->filters[$key]);

        return $this;
    }
}
