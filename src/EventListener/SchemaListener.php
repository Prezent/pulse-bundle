<?php

namespace Prezent\PulseBundle\EventListener;

use Prezent\PulseBundle\Schema\EventSchema;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;

/**
 * Merge schemas
 *
 * @author Sander Marechal
 */
class SchemaListener
{
    /**
     * Add the schemas
     *
     * @param GenerateSchemaEventArgs $args
     * @return void
     */
    public function postGenerateSchema(GenerateSchemaEventArgs $args)
    {
        EventSchema::addToSchema($args->getSchema());
    }
}
