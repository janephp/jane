<?php

namespace Joli\Jane\JsonSchema\Registry;

class Schema
{
    /** @var string */
    private $name;

    /** @var string */
    private $namespace;

    /** @var Model[] */
    private $models = [];

    /** @var string */
    private $directory;

    /** @var string */
    private $rootName;

    /**
     * @param string $name
     * @param string $namespace
     * @param string $directory
     * @param string $rootName
     */
    public function __construct($name, $namespace, $directory, $rootName)
    {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->directory = $directory;
        $this->rootName = $rootName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Model $model
     */
    public function addModel(Model $model)
    {
        $this->models[$model->getName()] = $model;
    }

    /**
     * @return string[]
     */
    public function getModels()
    {
        return array_keys($this->models);
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
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
    public function getRootName()
    {
        return $this->rootName;
    }

    /**
     * Return the model in this schema or null if not found
     *
     * @param string $classFqdn
     *
     * @return Model|null
     */
    public function getModel($classFqdn)
    {
        foreach ($this->models as $model) {
            if ($this->getNamespace() . '\\' . $model->getName() === $classFqdn) {
                return $model;
            }
        }

        return null;
    }

    /**
     * Return the model in this schema or null if not found
     *
     * @param string $reference
     *
     * @return Model|null
     */
    public function getModelByReference($reference)
    {
        foreach ($this->models as $model) {
            if ($model->getReference() === $reference) {
                return $model;
            }
        }

        return null;
    }
}
