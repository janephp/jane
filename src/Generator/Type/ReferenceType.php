<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\TypeDecisionManager;
use Joli\Jane\Reference\Reference;
use Joli\Jane\Reference\Resolver;

class ReferenceType extends AbstractType
{
    private $resolver;

    private $typeDecisionManager;

    public function __construct(Resolver $resolver, TypeDecisionManager $typeDecisionManager)
    {
        $this->resolver = $resolver;
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
        return ($schema instanceof Reference);
    }

    /**
     * {@inheritDoc}
     */
    public function generateDenormalizationLine($schema, $name, Context $context, $mode = self::SET_OBJECT)
    {
        $newSchema = $this->resolver->resolve($schema, $context->getRootSchema());

        if ($context->getSchemaObjectMap()->hasSchema($newSchema)) {
            return parent::generateDenormalizationLine($schema, $name, $context, $mode);
        }

        return $this->typeDecisionManager->resolveType($newSchema)->generateDenormalizationLine($newSchema, $name, $context, $mode);
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationValuePattern($schema, $name, Context $context)
    {
        $schema = $this->resolver->resolve($schema, $context->getRootSchema());

        if ($context->getSchemaObjectMap()->hasSchema($schema)) {
            $fqdn = "\\" . $context->getSchemaObjectMap()->getObject($schema)->getFullyQualifiedName();

            return sprintf('$this->denormalize(%%s, \'%s\', \'json\')', $fqdn);
        }

        return $this->typeDecisionManager->resolveType($schema)->getDenormalizationValuePattern($schema, $name, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function getRawCheck($schema, $name, Context $context)
    {
        $schema = $this->resolver->resolve($schema, $context->getRootSchema());

        return $this->typeDecisionManager->resolveType($schema)->getRawCheck($schema, $name, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpTypes($schema, $name, Context $context)
    {
        $schema = $this->resolver->resolve($schema, $context->getRootSchema());

        if ($context->getSchemaObjectMap()->hasSchema($schema)) {
            return ["\\" . $context->getSchemaObjectMap()->getObject($schema)->getFullyQualifiedName()];
        }

        return $this->typeDecisionManager->resolveType($schema)->getPhpTypes($schema, $name, $context);
    }
}
 