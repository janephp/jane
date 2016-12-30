<?php

declare(strict_types = 1);

namespace Joli\Jane\JsonSchema\Registry;

use Joli\Jane\AstGenerator\Extractor\ClassInfoExtractorInterface;
use League\Uri\Schemes\Http;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * Json Schema Registry
 */
class Registry implements ClassInfoExtractorInterface, PropertyInfoExtractorInterface
{
    /** @var Schema[] */
    private $schemas = [];

    /**
     * @param Schema $schema
     */
    public function addSchema(Schema $schema)
    {
        $this->schemas[$schema->getName()] = $schema;
    }

    /**
     * @return Schema[]
     */
    public function getSchemas()
    {
        return $this->schemas;
    }

    /**
     * {@inheritdoc}
     */
    public function getClasses($domain)
    {
        return $this->schemas[$domain]->getModels();
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace($domain, $class)
    {
        return $this->schemas[$domain]->getNamespace();
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
     * Get schema for a specific reference
     *
     * @param string $reference
     *
     * @return Schema|null
     */
    public function getSchema($reference)
    {
        if (array_key_exists($reference, $this->schemas)) {
            return $this->schemas[$reference];
        }

        return null;
    }

    /**
     * @param string $classFqdn
     *
     * @return Model
     */
    private function getModel($classFqdn)
    {
        foreach ($this->schemas as $schema) {
            $model = $schema->getModel($classFqdn);

            if (null !== $model) {
                return $model;
            }
        }

        throw new \RuntimeException('Model not found');
    }
}
