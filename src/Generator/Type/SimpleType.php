<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Reference\Reference;

abstract class SimpleType extends AbstractType
{
    /**
     * Return simple type name
     *
     * @return string
     */
    abstract public function getSimpleType();

    /**
     * {@inheritDoc}
     */
    public function supportSchema($schema)
    {
        return (!($schema instanceof Reference) && $schema->getType() === $this->getSimpleType());
    }

    /**
     * {@inheritDoc}
     */
    public function generateObject($schema, $name, Context $context)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function generateNormalizer($schema, $name, Context $context)
    {
    }
}
