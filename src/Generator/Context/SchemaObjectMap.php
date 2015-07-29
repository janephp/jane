<?php

namespace Joli\Jane\Generator\Context;

use Joli\Jane\Model\JsonSchema;
use PhpParser\Builder\Class_;

class SchemaObjectMap
{
    /**
     * @var Object[]
     */
    private $references;

    /**
     * @param JsonSchema  $schema
     * @param string      $object
     */
    public function addSchemaObject(JsonSchema $schema, $object)
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
     * @return string
     */
    public function getObject(JsonSchema $schema)
    {
        return $this->references[spl_object_hash($schema)];
    }

    /**
     * @return Class_[]
     */
    public function getReferences()
    {
        return $this->references;
    }
}
