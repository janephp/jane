<?php

namespace Joli\Jane\Generator\Context;

use Joli\Jane\Model\JsonSchema;
use Memio\Model\Object;

class SchemaObjectMap
{
    /**
     * @var Object[]
     */
    private $references;

    /**
     * @param JsonSchema          $schema
     * @param \Memio\Model\Object $object
     */
    public function addSchemaObject(JsonSchema $schema, Object $object)
    {
        $this->references[spl_object_hash($schema)] = $object;
    }

    /**
     * @param JsonSchema $schema
     *
     * @return bool
     */
    public function hasSchema(JsonSchema $schema)
    {
        return isset($this->references[spl_object_hash($schema)]);
    }

    /**
     * @param JsonSchema $schema
     *
     * @return \Memio\Model\Object
     */
    public function getObject(JsonSchema $schema)
    {
        return $this->references[spl_object_hash($schema)];
    }
}
 