<?php

namespace Prezent\PulseBundle\Query;

use Doctrine\DBAL\Driver\ResultStatement;

/**
 * Query result set
 *
 * @author Sander Marechal
 */
class ResultSet implements \IteratorAggregate, \Countable
{
    /**
     * @var array
     */
    private $aggregates = [];

    /**
     * @var string
     */
    private $totals = [];

    /**
     * Private constructor
     *
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * Create result set from statement
     *
     * @param ResultStatement $stmt
     * @return ResultSet
     */
    public static function fromStatement(ResultStatement $stmt)
    {
        $result = new static();

        foreach ($stmt as $row) {
            $result->addRow($row);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return array_sum(array_map(function ($group) {
            return count($group);
        }, $this->aggregates));
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        foreach ($this->aggregates as $group) {
            foreach ($group as $aggregate) {
                yield $aggregate;
            }
        }
    }

    /**
     * Get columns
     *
     * @return array
     */
    public function getColumns()
    {
        return array_keys($this->totals);
    }

    /**
     * Get column totals
     *
     * @return array
     */
    public function getColumnTotals()
    {
        return $this->totals;
    }

    /**
     * Get grand total
     *
     * @return int
     */
    public function getTotal()
    {
        return array_sum($this->totals);
    }

    /**
     * Add a result row to the set
     *
     * @param array $row
     * @return void
     */
    private function addRow(array $row)
    {
        if (!isset($this->aggregates[$row['key']][$row['id']])) {
            $this->aggregates[$row['key']][$row['id']] = new Aggregate($this, $row['key'], $row['id']);
        }

        $this->aggregates[$row['key']][$row['id']]->addRow($row);

        if (!isset($this->totals[$row['type']])) {
            $this->totals[$row['type']] = 0;
        }

        $this->totals[$row['type']] += $row['total'];
    }
}
