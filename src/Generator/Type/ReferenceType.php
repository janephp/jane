<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\TypeDecisionManager;
use Joli\Jane\Reference\Reference;
use Joli\Jane\Reference\Resolver;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\Node\Scalar;

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
    public function getDenormalizationStmt($schema, $name, Context $context, Expr $input)
    {
        $newSchema = $this->resolver->resolve($schema);

        if ($context->getSchemaObjectMap()->hasSchema($newSchema)) {
            if (!$context->getSchemaObjectNormalizerMap()->hasSchema($newSchema)) {
                $this->typeDecisionManager->resolveType($newSchema)->generateNormalizer($newSchema, $context->getSchemaObjectMap()->getObject($newSchema), $context);
            }

            return parent::getDenormalizationStmt($schema, $name, $context, $input);
        }

        return $this->typeDecisionManager->resolveType($newSchema)->getDenormalizationStmt($newSchema, $name, $context, $input);
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationValueStmt($schema, $name, Context $context, Expr $input)
    {
        $schema = $this->resolver->resolve($schema);

        if ($context->getSchemaObjectMap()->hasSchema($schema)) {
            $fqdn = $context->getNamespace() . '\\Model\\'. $context->getSchemaObjectMap()->getObject($schema);

            // $this->normalizerChain->denormalize(...)
            return new Expr\MethodCall(new Expr\PropertyFetch(new Expr\Variable('this'), 'normalizerChain'), 'denormalize', [
                new Arg($input),
                new Arg(new Scalar\String_($fqdn)),
                new Arg(new Scalar\String_('json')),
                new Arg(new Expr\Variable('context'))
            ]);
        }

        return $this->typeDecisionManager->resolveType($schema)->getDenormalizationValueStmt($schema, $name, $context, $input);
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationIfStmt($schema, $name, Context $context, Expr $input)
    {
        $schema = $this->resolver->resolve($schema, $context->getRootSchema());

        return $this->typeDecisionManager->resolveType($schema)->getDenormalizationIfStmt($schema, $name, $context, $input);
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpTypes($schema, $name, Context $context)
    {
        $newSchema = $this->resolver->resolve($schema, $context->getRootSchema());

        if ($context->getSchemaObjectMap()->hasSchema($newSchema)) {
            return [$context->getSchemaObjectMap()->getObject($newSchema)];
        }

        return $this->typeDecisionManager->resolveType($newSchema)->getPhpTypes($newSchema, $name, $context);
    }
}
