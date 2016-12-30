<?php

declare(strict_types = 1);

namespace Joli\Jane\JsonSchema\Registry;

use Joli\Jane\AstGenerator\Extractor\ClassInfoExtractorInterface;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * Json Schema Registry
 */
class Registry implements ClassInfoExtractorInterface, PropertyInfoExtractorInterface
{

    public function registerSchema($schema)
    {

    }

    public function registerModel($schema, $model)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getClasses($domain)
    {
        // TODO: Implement getClasses() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace($domain, $class)
    {
        // TODO: Implement getNamespace() method.
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable($class, $property, array $context = array())
    {
        return $this->getModel($class)->getProperty($property)->isReadable();
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable($class, $property, array $context = array())
    {
        return $this->getModel($class)->getProperty($property)->isWritable();
    }

    /**
     * {@inheritdoc}
     */
    public function getShortDescription($class, $property, array $context = array())
    {
        return $this->getModel($class)->getProperty($property)->getShortDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function getLongDescription($class, $property, array $context = array())
    {
        return $this->getModel($class)->getProperty($property)->getLongDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties($class, array $context = array())
    {
        return $this->getModel($class)->getProperties();
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes($class, $property, array $context = array())
    {
        return $this->getModel($class)->getProperty($property)->getTypes();
    }

    /**
     * @param string $classFqdn
     *
     * @return Model
     */
    private function getModel($classFqdn)
    {

    }
}
