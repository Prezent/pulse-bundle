<?php

namespace Prezent\PulseBundle\Repository;

use Doctrine\DBAL\Driver\Connection;
use Prezent\PulseBundle\Event\PulseEvent;
use Prezent\PulseBundle\Query\Query;
use Prezent\PulseBundle\Query\ResultSet;

/**
 * EventRepository
 *
 * @author Sander Marechal
 */
class EventRepository
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * Constructor
     *
     * @param Connection $conn
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Append an event
     *
     * @param PulseEvent $event
     * @return void
     */
    public function append(PulseEvent $event)
    {
        $this->conn->transactional(function ($conn) use ($event) {
            // Append event
            $conn->insert('`pulse_event`', [
                'type'       => $event->getType(),
                'aggregates' => json_encode($event->getAggregates()),
                'date'       => $event->getDate(),
            ], ['string', 'text', 'datetime']);

            $this->updateAggregates($event);
        });
    }

    /**
     * Run a query
     *
     * @param Query $query
     * @return ResultSet
     */
    public function query(Query $query)
    {
        $qb = $this->conn->createQueryBuilder()
            ->select(['`type`', '`key`', '`id`', 'SUM(`cnt`) as `total`'])
            ->from('`pulse_aggregate`')
            ->groupBy(['`type`', '`key`', '`id`'])
        ;

        if ($date = $query->getStartDate()) {
            $qb->andWhere('`date` >= ' . $qb->createNamedParameter($date, 'date'));
        }

        if ($date = $query->getEndDate()) {
            $qb->andWhere('`date` <= ' . $qb->createNamedParameter($date, 'date'));
        }

        $filters = [];
        $expr = $qb->expr();

        foreach ($query->getFilters() as $key => $ids) {
            if ($ids) {
                $filters[] = $expr->andX(
                    $expr->eq('`key`', $qb->createNamedParameter($key)),
                    $expr->in('`id`', $ids)
                );
            } else {
                $filters[] = $expr->eq('`key`', $qb->createNamedParameter($key));
            }
        }

        if ($filters) {
            $qb->andWhere($expr->orX(...$filters));
        }

        return ResultSet::fromStatement($this->conn->executeQuery($qb->getSQL(), $qb->getParameters()));
    }

    /**
     * Replay all aggregates from stored events
     *
     * @return void
     */
    public function replayAgggregates()
    {
        $this->conn->transactional(function ($conn) {
            $conn->executeUpdate('DELETE FROM `pulse_aggregate`');
            $stmt = $conn->executeQuery('SELECT `type`, `aggregates`, `date` FROM `pulse_event` ORDER BY id ASC');

            foreach ($stmt as $row) {
                $this->updateAggregates(new PulseEvent(
                    $row['type'],
                    json_decode($row['aggregates'], true),
                    new \DateTime($row['date'])
                ));
            }
        });
    }

    /**
     * Update aggregates for an event
     *
     * @param PulseEvent $event
     * @return void
     */
    private function updateAggregates(PulseEvent $event)
    {
        $this->conn->transactional(function ($conn) use ($event) {
            $stmt = $conn->prepare('INSERT INTO `pulse_aggregate` (`type`, `key`, `id`, `date`, `cnt`)
                VALUES (:type, :key, :id, :date, :cnt)
                ON DUPLICATE KEY UPDATE `cnt` = `cnt` + 1');

            foreach ($event->getAggregates() as $key => $ids) {
                if (!is_array($ids)) {
                    $ids = [$ids];
                }

                foreach ($ids as $id) {
                    $stmt->bindValue('type', $event->getType(), 'string');
                    $stmt->bindValue('key', $key, 'string');
                    $stmt->bindValue('id', $id, 'string');
                    $stmt->bindValue('date', $event->getDate(), 'date');
                    $stmt->bindValue('cnt', 1, 'integer');

                    $stmt->execute();
                }
            }
        });
    }
}
