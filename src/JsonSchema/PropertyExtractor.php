<?php

declare(strict_types=1);

namespace Joli\Jane\JsonSchema;

use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

class PropertyExtractor implements PropertyInfoExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isReadable($class, $property, array $context = [])
    {
        // TODO: Implement isReadable() method.
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable($class, $property, array $context = [])
    {
        // TODO: Implement isWritable() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getShortDescription($class, $property, array $context = [])
    {
        // TODO: Implement getShortDescription() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getLongDescription($class, $property, array $context = [])
    {
        // TODO: Implement getLongDescription() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties($class, array $context = [])
    {
        // TODO: Implement getProperties() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes($class, $property, array $context = [])
    {
        // TODO: Implement getTypes() method.
    }
}
