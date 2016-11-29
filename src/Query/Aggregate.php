<?php

namespace Prezent\PulseBundle\Query;

/**
 * Query result row
 *
 * @author Sander Marechal
 */
class Aggregate implements \IteratorAggregate, \Countable
{
    /**
     * @var ResultSet
     */
    private $resultSet;

    /**
     * @var string
     */
    private $key;

    /**
     * @var mixed
     */
    private $id;

    /**
     * @var array
     */
    private $columns = [];

    /**
     * @var string
     */
    private $total = 0;

    /**
     * Constructor
     *
     * @param ResultSet $resultSet
     * @param string $key
     * @param mixed $id
     */
    public function __construct(ResultSet $resultSet, $key, $id)
    {
        $this->resultSet = $resultSet;
        $this->key = $key;
        $this->id = $id;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add a row to the aggregate
     *
     * @param array $row
     * @return void
     */
    public function addRow(array $row)
    {
        if ($this->key != $row['key'] || $this->id != $row['id']) {
            throw new \InvalidArgumentException(sprintf(
                'The row must be for key "%s", id "%s"',
                $row['key'],
                $row['id']
            ));
        }

        if (!isset($this->columns[$row['type']])) {
            $this->columns[$row['type']] = 0;
        }

        $this->columns[$row['type']] += $row['total'];
        $this->total += $row['total'];
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->columns);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        foreach ($this->getColumns() as $column) {
            yield $column => isset($this->columns[$column])
                ? $this->columns[$column]
                : 0;
        }
    }

    /**
     * Get columns
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->resultSet->getColumns();
    }

    /**
     * Get total
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }
}
