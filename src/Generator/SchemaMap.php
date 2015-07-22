<?php

namespace Joli\Jane\Generator;

use Joli\Jane\Schema\Schema;
use Memio\Model\Object;

class SchemaObjectMap
{
    /**
     * @var Object[]
     */
    private $references;

    /**
     * @param Schema $schema
     * @param Object $object
     */
    public function addSchemaObject(Schema $schema, Object $object)
    {
        $this->references[spl_object_hash($schema)] = $object;
    }

    /**
     * @param Schema $schema
     *
     * @return bool
     */
    public function hasSchema(Schema $schema)
    {
        return isset($this->references[spl_object_hash($schema)]);
    }

    /**
     * @param Schema $schema
     *
     * @return Object
     */
    public function getObject(Schema $schema)
    {
        return $this->references[spl_object_hash($schema)];
    }
}
 