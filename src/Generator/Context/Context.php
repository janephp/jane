<?php

namespace Joli\Jane\Generator\Context;

use Joli\Jane\Model\JsonSchema;
use Memio\Model\File;

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
     * @var \Joli\Jane\Model\JsonSchema
     */
    private $rootSchema;

    /**
     * @var SchemaObjectMap
     */
    private $schemaObjectMap;

    /**
     * @var SchemaObjectMap
     */
    private $schemaObjectNormalizerMap;

    /**
     * @var File
     */
    private $files = [];

    /**
     * @param JsonSchema $rootSchema
     * @param string $namespace
     * @param string $directory
     */
    public function __construct(JsonSchema $rootSchema, $namespace, $directory)
    {
        $this->rootSchema      = $rootSchema;
        $this->namespace       = $namespace;
        $this->directory       = $directory;
        $this->schemaObjectMap = new SchemaObjectMap();
        $this->schemaObjectNormalizerMap = new SchemaObjectMap();
    }

    public function addFile(File $file)
    {
        $this->files[] = $file;
    }

    public function getFiles()
    {
        return $this->files;
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
     * @return JsonSchema
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

    /**
     * @return SchemaObjectMap
     */
    public function getSchemaObjectNormalizerMap()
    {
        return $this->schemaObjectNormalizerMap;
    }
}
 