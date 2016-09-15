<?php

namespace Joli\Jane\Model;

interface JsonSchemaInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     *
     * @return self
     */
    public function setId($id = null);

    /**
     * @return string
     */
    public function getDollarSchema();

    /**
     * @param string $dollarSchema
     *
     * @return self
     */
    public function setDollarSchema($dollarSchema = null);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return self
     */
    public function setTitle($title = null);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description = null);

    /**
     * @return mixed
     */
    public function getDefault();

    /**
     * @param mixed $default
     *
     * @return self
     */
    public function setDefault($default = null);

    /**
     * @return float
     */
    public function getMultipleOf();

    /**
     * @param float $multipleOf
     *
     * @return self
     */
    public function setMultipleOf($multipleOf = null);

    /**
     * @return float
     */
    public function getMaximum();

    /**
     * @param float $maximum
     *
     * @return self
     */
    public function setMaximum($maximum = null);

    /**
     * @return bool
     */
    public function getExclusiveMaximum();

    /**
     * @param bool $exclusiveMaximum
     *
     * @return self
     */
    public function setExclusiveMaximum($exclusiveMaximum = null);

    /**
     * @return float
     */
    public function getMinimum();

    /**
     * @param float $minimum
     *
     * @return self
     */
    public function setMinimum($minimum = null);

    /**
     * @return bool
     */
    public function getExclusiveMinimum();

    /**
     * @param bool $exclusiveMinimum
     *
     * @return self
     */
    public function setExclusiveMinimum($exclusiveMinimum = null);

    /**
     * @return int
     */
    public function getMaxLength();

    /**
     * @param int $maxLength
     *
     * @return self
     */
    public function setMaxLength($maxLength = null);

    /**
     * @return int
     */
    public function getMinLength();

    /**
     * @param int $minLength
     *
     * @return self
     */
    public function setMinLength($minLength = null);

    /**
     * @return string
     */
    public function getPattern();

    /**
     * @param string $pattern
     *
     * @return self
     */
    public function setPattern($pattern = null);

    /**
     * @return bool|JsonSchema
     */
    public function getAdditionalItems();

    /**
     * @param bool|JsonSchema $additionalItems
     *
     * @return self
     */
    public function setAdditionalItems($additionalItems = null);

    /**
     * @return JsonSchema|JsonSchema[]
     */
    public function getItems();

    /**
     * @param JsonSchema|JsonSchema[] $items
     *
     * @return self
     */
    public function setItems($items = null);

    /**
     * @return int
     */
    public function getMaxItems();

    /**
     * @param int $maxItems
     *
     * @return self
     */
    public function setMaxItems($maxItems = null);

    /**
     * @return int
     */
    public function getMinItems();

    /**
     * @param int $minItems
     *
     * @return self
     */
    public function setMinItems($minItems = null);

    /**
     * @return bool
     */
    public function getUniqueItems();

    /**
     * @param bool $uniqueItems
     *
     * @return self
     */
    public function setUniqueItems($uniqueItems = null);

    /**
     * @return int
     */
    public function getMaxProperties();

    /**
     * @param int $maxProperties
     *
     * @return self
     */
    public function setMaxProperties($maxProperties = null);

    /**
     * @return int
     */
    public function getMinProperties();

    /**
     * @param int $minProperties
     *
     * @return self
     */
    public function setMinProperties($minProperties = null);

    /**
     * @return string[]
     */
    public function getRequired();

    /**
     * @param string[] $required
     *
     * @return self
     */
    public function setRequired(array $required = null);

    /**
     * @return bool|JsonSchema
     */
    public function getAdditionalProperties();

    /**
     * @param bool|JsonSchema $additionalProperties
     *
     * @return self
     */
    public function setAdditionalProperties($additionalProperties = null);

    /**
     * @return JsonSchema[]
     */
    public function getDefinitions();

    /**
     * @param JsonSchema[] $definitions
     *
     * @return self
     */
    public function setDefinitions(\ArrayObject $definitions = null);

    /**
     * @return JsonSchema[]
     */
    public function getProperties();

    /**
     * @param JsonSchema[] $properties
     *
     * @return self
     */
    public function setProperties(\ArrayObject $properties = null);

    /**
     * @return JsonSchema[]
     */
    public function getPatternProperties();

    /**
     * @param JsonSchema[] $patternProperties
     *
     * @return self
     */
    public function setPatternProperties(\ArrayObject $patternProperties = null);

    /**
     * @return JsonSchema[]|string[][]
     */
    public function getDependencies();

    /**
     * @param JsonSchema[]|string[][] $dependencies
     *
     * @return self
     */
    public function setDependencies(\ArrayObject $dependencies = null);

    /**
     * @return mixed[]
     */
    public function getEnum();

    /**
     * @param mixed[] $enum
     *
     * @return self
     */
    public function setEnum(array $enum = null);

    /**
     * @return mixed|mixed[]
     */
    public function getType();

    /**
     * @param mixed|mixed[] $type
     *
     * @return self
     */
    public function setType($type = null);

    /**
     * @return string
     */
    public function getFormat();

    /**
     * @param string $format
     *
     * @return self
     */
    public function setFormat($format = null);

    /**
     * @return JsonSchema[]
     */
    public function getAllOf();

    /**
     * @param JsonSchema[] $allOf
     *
     * @return self
     */
    public function setAllOf(array $allOf = null);

    /**
     * @return JsonSchema[]
     */
    public function getAnyOf();

    /**
     * @param JsonSchema[] $anyOf
     *
     * @return self
     */
    public function setAnyOf(array $anyOf = null);

    /**
     * @return JsonSchema[]
     */
    public function getOneOf();

    /**
     * @param JsonSchema[] $oneOf
     *
     * @return self
     */
    public function setOneOf(array $oneOf = null);

    /**
     * @return JsonSchema
     */
    public function getNot();

    /**
     * @param JsonSchema $not
     *
     * @return self
     */
    public function setNot(JsonSchemaInterface $not = null);
}
