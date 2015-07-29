<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Reference\Reference;
use Joli\Jane\Model\JsonSchema;
use PhpParser\Builder\Method;
use PhpParser\Builder\Property;
use PhpParser\Node\Expr;

interface TypeInterface
{
    const SET_OBJECT = 0;
    const SET_ARRAY  = 1;

    /**
     * Generate an object if needed
     *
     * @param JsonSchema|Reference $schema
     * @param Context          $context
     * @param string $name
     *
     * @return mixed|null
     */
    public function generateObject($schema, $name, Context $context);

    /**
     * Generate an normalizer
     *
     * @param JsonSchema|Reference $schema
     * @param Context          $context
     * @param string $name
     *
     * @return mixed|null
     */
    public function generateNormalizer($schema, $name, Context $context);

    /**
     * Generate a property given a schema and a name
     *
     * @param JsonSchema|Reference $schema
     * @param Context          $context
     * @param string $name
     *
     * @return Property
     */
    public function getProperty($schema, $name, Context $context);

    /**
     * Generate methods associated to this schema
     *
     * @param JsonSchema|Reference $schema
     * @param Context          $context
     * @param string $name
     *
     * @return Method[]
     */
    public function getMethods($schema, $name, Context $context);

    /**
     * Generate line of code used in denormalization
     *
     * @param JsonSchema|Reference $schema
     * @param Context          $context
     * @param string $name
     * @param Expr $input
     *
     * @return [Expr[], Expr] Return an array of statement, and the output expression
     */
    public function getDenormalizationStmt($schema, $name, Context $context, Expr $input);

    /**
     *
     * @param JsonSchema|Reference $schema
     * @param string $name
     * @param Context $context
     * @param Expr $input
     *
     * @return string
     */
    public function getDenormalizationValueStmt($schema, $name, Context $context, Expr $input);

    /**
     * Generate if statment when we must choose for this type in denormalization
     *
     * @param JsonSchema|Reference $schema
     * @param string $name
     * @param Context $context
     * @param Expr $input
     *
     * @return Expr
     */
    public function getDenormalizationIfStmt($schema, $name, Context $context, Expr $input);

    /**
     * Whether this schema is supported
     *
     * @param JsonSchema|Reference $schema
     *
     * @return boolean
     */
    public function supportSchema($schema);

    /**
     * Return associated php type
     *
     * @param JsonSchema|Reference $schema
     * @param Context          $context
     * @param string $name
     *
     * @return string[]
     */
    public function getPhpTypes($schema, $name, Context $context);
}
