<?php

namespace Joli\Jane\Generator\Context;

use Joli\Jane\Schema\Schema;

/**
 * Context when generating a library base on a Schema
 */
class Context
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var \Joli\Jane\Schema\Schema
     */
    private $rootSchema;

    /**
     * @var SchemaObjectMap
     */
    private $schemaObjectMap;

    /**
     * @param Schema $rootSchema
     * @param string $namespace
     * @param string $directory
     */
    public function __construct(Schema $rootSchema, $namespace, $directory)
    {
        $this->rootSchema      = $rootSchema;
        $this->namespace       = $namespace;
        $this->directory       = $directory;
        $this->schemaObjectMap = new SchemaObjectMap();
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return Schema
     */
    public function getRootSchema()
    {
        return $this->rootSchema;
    }

    /**
     * @return SchemaObjectMap
     */
    public function getSchemaObjectMap()
    {
        return $this->schemaObjectMap;
    }
}
 