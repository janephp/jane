<?php

namespace Joli\Jane\Schema;

class Schema
{
    /**
     * @var string
     *
     * format: uri
     */
    protected $id;

    /**
     * @var string
     *
     * format: uri
     */
    protected $schema;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var mixed
     *
     * Default data value
     */
    protected $default;

    /**
     * @var integer
     */
    protected $multipleOf;

    /**
     * @var integer
     */
    protected $maximum;

    /**
     * @var boolean
     */
    protected $exclusiveMaximum;

    /**
     * @var integer
     */
    protected $minimum;

    /**
     * @var boolean
     */
    protected $exclusiveMinimum;

    /**
     * @var integer
     *
     * positive Integer
     */
    protected $maxLength;

    /**
     * @var integer
     *
     * positive Integer, default to 0
     */
    protected $minLength = 0;

    /**
     * @var string
     *
     * format: regex
     */
    protected $pattern;

    /**
     * @var boolean|Schema
     */
    protected $additionalItems;

    /**
     * @var Schema|array
     */
    protected $items;

    /**
     * @var integer
     */
    protected $maxItems;

    /**
     * @var integer
     */
    protected $minItems;

    /**
     * @var boolean
     */
    protected $uniqueItems = false;

    /**
     * @var integer
     *
     * positive Integer
     */
    protected $maxProperties;

    /**
     * @var integer
     *
     * positive Integer, default to 0
     */
    protected $minProperties = 0;

    /**
     * @var array<string>
     *
     * unique items, minimum of 1 item
     */
    protected $required;

    /**
     * @var boolean|Schema
     */
    protected $additionalProperties;

    /**
     * @var Schema[]
     */
    protected $definitions = array();

    /**
     * @var Schema[]
     */
    protected $properties = array();

    /**
     * @var Schema[]
     */
    protected $patternProperties = array();

    /**
     * @var Schema[]|array<string>[]
     *
     * Each value can be a schema or an array of string,
     * if it's an array of string then it must a one value, and all values must be unique
     */
    protected $dependencies;

    /**
     * @var array
     *
     * Minimum of 1 value, all values must be unique
     */
    protected $enum;

    /**
     * @var string|array<string>
     */
    protected $type;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var Schema[]
     */
    protected $allOf;

    /**
     * @var Schema[]
     */
    protected $anyOf;

    /**
     * @var Schema[]
     */
    protected $oneOf;

    /**
     * @var Schema
     */
    protected $not;

    /**
     * @return bool|Schema
     */
    public function getAdditionalItems()
    {
        return $this->additionalItems;
    }

    /**
     * @param bool|Schema $additionalItems
     */
    public function setAdditionalItems($additionalItems)
    {
        $this->additionalItems = $additionalItems;
    }

    /**
     * @return bool|Schema
     */
    public function getAdditionalProperties()
    {
        return $this->additionalProperties;
    }

    /**
     * @param bool|Schema $additionalProperties
     */
    public function setAdditionalProperties($additionalProperties)
    {
        $this->additionalProperties = $additionalProperties;
    }

    /**
     * @return Schema[]
     */
    public function getAllOf()
    {
        return $this->allOf;
    }

    /**
     * @param Schema[] $allOf
     */
    public function setAllOf($allOf)
    {
        $this->allOf = $allOf;
    }

    /**
     * @return Schema[]
     */
    public function getAnyOf()
    {
        return $this->anyOf;
    }

    /**
     * @param Schema[] $anyOf
     */
    public function setAnyOf($anyOf)
    {
        $this->anyOf = $anyOf;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return Schema[]
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * @param Schema[] $definitions
     */
    public function setDefinitions($definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * @return array|Schema[]
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @param array|Schema[] $dependencies
     */
    public function setDependencies($dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getEnum()
    {
        return $this->enum;
    }

    /**
     * @param array $enum
     */
    public function setEnum($enum)
    {
        $this->enum = $enum;
    }

    /**
     * @return boolean
     */
    public function getExclusiveMaximum()
    {
        return $this->exclusiveMaximum;
    }

    /**
     * @param boolean $exclusiveMaximum
     */
    public function setExclusiveMaximum($exclusiveMaximum)
    {
        $this->exclusiveMaximum = $exclusiveMaximum;
    }

    /**
     * @return boolean
     */
    public function getExclusiveMinimum()
    {
        return $this->exclusiveMinimum;
    }

    /**
     * @param boolean $exclusiveMinimum
     */
    public function setExclusiveMinimum($exclusiveMinimum)
    {
        $this->exclusiveMinimum = $exclusiveMinimum;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return array|Schema
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array|Schema $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return int
     */
    public function getMaxItems()
    {
        return $this->maxItems;
    }

    /**
     * @param int $maxItems
     */
    public function setMaxItems($maxItems)
    {
        $this->maxItems = $maxItems;
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @param int $maxLength
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * @return int
     */
    public function getMaxProperties()
    {
        return $this->maxProperties;
    }

    /**
     * @param int $maxProperties
     */
    public function setMaxProperties($maxProperties)
    {
        $this->maxProperties = $maxProperties;
    }

    /**
     * @return int
     */
    public function getMaximum()
    {
        return $this->maximum;
    }

    /**
     * @param int $maximum
     */
    public function setMaximum($maximum)
    {
        $this->maximum = $maximum;
    }

    /**
     * @return int
     */
    public function getMinItems()
    {
        return $this->minItems;
    }

    /**
     * @param int $minItems
     */
    public function setMinItems($minItems)
    {
        $this->minItems = $minItems;
    }

    /**
     * @return int
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     * @param int $minLength
     */
    public function setMinLength($minLength)
    {
        $this->minLength = $minLength;
    }

    /**
     * @return int
     */
    public function getMinProperties()
    {
        return $this->minProperties;
    }

    /**
     * @param int $minProperties
     */
    public function setMinProperties($minProperties)
    {
        $this->minProperties = $minProperties;
    }

    /**
     * @return int
     */
    public function getMinimum()
    {
        return $this->minimum;
    }

    /**
     * @param int $minimum
     */
    public function setMinimum($minimum)
    {
        $this->minimum = $minimum;
    }

    /**
     * @return int
     */
    public function getMultipleOf()
    {
        return $this->multipleOf;
    }

    /**
     * @param int $multipleOf
     */
    public function setMultipleOf($multipleOf)
    {
        $this->multipleOf = $multipleOf;
    }

    /**
     * @return Schema
     */
    public function getNot()
    {
        return $this->not;
    }

    /**
     * @param Schema $not
     */
    public function setNot($not)
    {
        $this->not = $not;
    }

    /**
     * @return Schema[]
     */
    public function getOneOf()
    {
        return $this->oneOf;
    }

    /**
     * @param Schema[] $oneOf
     */
    public function setOneOf($oneOf)
    {
        $this->oneOf = $oneOf;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return Schema[]
     */
    public function getPatternProperties()
    {
        return $this->patternProperties;
    }

    /**
     * @param Schema[] $patternProperties
     */
    public function setPatternProperties($patternProperties)
    {
        $this->patternProperties = $patternProperties;
    }

    /**
     * @return Schema[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param Schema[] $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return array
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param array $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param string $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return array|string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param array|string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return boolean
     */
    public function getUniqueItems()
    {
        return $this->uniqueItems;
    }

    /**
     * @param boolean $uniqueItems
     */
    public function setUniqueItems($uniqueItems)
    {
        $this->uniqueItems = $uniqueItems;
    }
}
