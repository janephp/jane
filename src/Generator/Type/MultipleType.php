<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\TypeDecisionManager;
use Joli\Jane\Model\JsonSchema;

class MultipleType extends AbstractType
{
    /**
     * @var \Joli\Jane\Generator\TypeDecisionManager
     */
    private $typeDecisionManager;

    public function __construct(TypeDecisionManager $typeDecisionManager)
    {
        $this->typeDecisionManager = $typeDecisionManager;
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

    /**
     * {@inheritDoc}
     */
    public function supportSchema($schema)
    {
        return ($schema instanceof JsonSchema && is_array($schema->getType()));
    }

    /**
     * {@inheritDoc}
     */
    public function getRawCheck($schema, $name, Context $context)
    {
        $check = [];
        $fakeSchema = new JsonSchema();

        foreach ($schema->getType() as $type) {
            $fakeSchema->setType($type);
            $check[] = $this->typeDecisionManager->resolveType($fakeSchema)->getRawCheck($fakeSchema, $name, $context);
        }

        return implode(' || ', $check);
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpTypes($schema, $name, Context $context)
    {
        $types = [];
        $fakeSchema = new JsonSchema();

        foreach ($schema->getType() as $type) {
            $fakeSchema->setType($type);
            $types = array_merge($types, $this->typeDecisionManager->resolveType($fakeSchema)->getPhpTypes($fakeSchema, $name, $context));
        }

        return $types;
    }
}
