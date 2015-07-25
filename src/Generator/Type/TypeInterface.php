<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Reference\Reference;
use Joli\Jane\Model\JsonSchema;
use Memio\Model\Method;
use Memio\Model\Property;

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
    public function generateProperty($schema, $name, Context $context);

    /**
     * Generate methods associated to this schema
     *
     * @param JsonSchema|Reference $schema
     * @param Context          $context
     * @param string $name
     *
     * @return Method[]
     */
    public function generateMethods($schema, $name, Context $context);

    /**
     * Generate line of code used in denormalization
     *
     * @param JsonSchema|Reference $schema
     * @param Context          $context
     * @param string $name
     * @param integer $mode
     *
     * @return string
     */
    public function generateDenormalizationLine($schema, $name, Context $context, $mode = self::SET_OBJECT);

    /**
     *
     * @param JsonSchema|Reference $schema
     * @param Context          $context
     * @param string $name
     *
     * @return string
     */
    public function getDenormalizationValuePattern($schema, $name, Context $context);

    /**
     * Generate line of code used in denormalization
     *
     * @param JsonSchema|Reference $schema
     * @param Context          $context
     * @param string $name
     *
     * @return string
     */
    public function getRawCheck($schema, $name, Context $context);

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
 