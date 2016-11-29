<?php

namespace Prezent\PulseBundle\Schema;

use Doctrine\DBAL\Schema\Schema;

/**
 * DBAL Schema for events
 *
 * @author Sander Marechal
 */
class EventSchema
{
    /**
     * Add all tables to a schema
     *
     * @param Schema $schema
     * @return void
     */
    public static function addToSchema(Schema $schema)
    {
        self::addAggregateTable($schema);
        self::addEventTable($schema);
    }

    /**
     * Add aggregate table
     *
     * @param Schema $schema
     * @return void
     */
    private static function addAggregateTable(Schema $schema)
    {
        $table = $schema->createTable('pulse_aggregate');

        $table->addColumn('type', 'string', ['length' => 255]);
        $table->addColumn('key', 'string', ['length' => 255]);
        $table->addColumn('id', 'string', ['length' => 255]);
        $table->addColumn('date', 'date');
        $table->addColumn('cnt', 'integer');

        $table->setPrimaryKey(['type', 'key', 'id', 'date']);
        $table->addIndex(['key']);
        $table->addIndex(['date']);
    }

    /**
     * add event table
     *
     * @param Schema $schema
     * @return void
     */
    private static function addEventTable(Schema $schema)
    {
        $table = $schema->createTable('pulse_event');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->addColumn('aggregates', 'text');
        $table->addColumn('date', 'datetime');

        $table->setPrimaryKey(['id']);
    }
}
