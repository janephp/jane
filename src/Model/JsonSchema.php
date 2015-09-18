<?php

namespace Joli\Jane\Model;

class JsonSchema
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $dollarSchema;
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
     */
    protected $default;
    /**
     * @var float
     */
    protected $multipleOf;
    /**
     * @var float
     */
    protected $maximum;
    /**
     * @var bool
     */
    protected $exclusiveMaximum;
    /**
     * @var float
     */
    protected $minimum;
    /**
     * @var bool
     */
    protected $exclusiveMinimum;
    /**
     * @var int
     */
    protected $maxLength;
    /**
     * @var int
     */
    protected $minLength;
    /**
     * @var string
     */
    protected $pattern;
    /**
     * @var bool|JsonSchema
     */
    protected $additionalItems;
    /**
     * @var JsonSchema|JsonSchema[]
     */
    protected $items;
    /**
     * @var int
     */
    protected $maxItems;
    /**
     * @var int
     */
    protected $minItems;
    /**
     * @var bool
     */
    protected $uniqueItems;
    /**
     * @var int
     */
    protected $maxProperties;
    /**
     * @var int
     */
    protected $minProperties;
    /**
     * @var string[]
     */
    protected $required;
    /**
     * @var bool|JsonSchema
     */
    protected $additionalProperties;
    /**
     * @var JsonSchema[]
     */
    protected $definitions;
    /**
     * @var JsonSchema[]
     */
    protected $properties;
    /**
     * @var JsonSchema[]
     */
    protected $patternProperties;
    /**
     * @var JsonSchema[]|string[][]
     */
    protected $dependencies;
    /**
     * @var mixed[]
     */
    protected $enum;
    /**
     * @var mixed|mixed[]
     */
    protected $type;
    /**
     * @var string
     */
    protected $format;
    /**
     * @var JsonSchema[]
     */
    protected $allOf;
    /**
     * @var JsonSchema[]
     */
    protected $anyOf;
    /**
     * @var JsonSchema[]
     */
    protected $oneOf;
    /**
     * @var JsonSchema
     */
    protected $not;
    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @param string $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    /**
     * @return string
     */
    public function getDollarSchema()
    {
        return $this->dollarSchema;
    }
    /**
     * @param string $dollarSchema
     *
     * @return self
     */
    public function setDollarSchema($dollarSchema)
    {
        $this->dollarSchema = $dollarSchema;

        return $this;
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
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
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
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
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
     *
     * @return self
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }
    /**
     * @return float
     */
    public function getMultipleOf()
    {
        return $this->multipleOf;
    }
    /**
     * @param float $multipleOf
     *
     * @return self
     */
    public function setMultipleOf($multipleOf)
    {
        $this->multipleOf = $multipleOf;

        return $this;
    }
    /**
     * @return float
     */
    public function getMaximum()
    {
        return $this->maximum;
    }
    /**
     * @param float $maximum
     *
     * @return self
     */
    public function setMaximum($maximum)
    {
        $this->maximum = $maximum;

        return $this;
    }
    /**
     * @return bool
     */
    public function getExclusiveMaximum()
    {
        return $this->exclusiveMaximum;
    }
    /**
     * @param bool $exclusiveMaximum
     *
     * @return self
     */
    public function setExclusiveMaximum($exclusiveMaximum)
    {
        $this->exclusiveMaximum = $exclusiveMaximum;

        return $this;
    }
    /**
     * @return float
     */
    public function getMinimum()
    {
        return $this->minimum;
    }
    /**
     * @param float $minimum
     *
     * @return self
     */
    public function setMinimum($minimum)
    {
        $this->minimum = $minimum;

        return $this;
    }
    /**
     * @return bool
     */
    public function getExclusiveMinimum()
    {
        return $this->exclusiveMinimum;
    }
    /**
     * @param bool $exclusiveMinimum
     *
     * @return self
     */
    public function setExclusiveMinimum($exclusiveMinimum)
    {
        $this->exclusiveMinimum = $exclusiveMinimum;

        return $this;
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
     *
     * @return self
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;

        return $this;
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
     *
     * @return self
     */
    public function setMinLength($minLength)
    {
        $this->minLength = $minLength;

        return $this;
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
     *
     * @return self
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }
    /**
     * @return bool|JsonSchema
     */
    public function getAdditionalItems()
    {
        return $this->additionalItems;
    }
    /**
     * @param bool|JsonSchema $additionalItems
     *
     * @return self
     */
    public function setAdditionalItems($additionalItems)
    {
        $this->additionalItems = $additionalItems;

        return $this;
    }
    /**
     * @return JsonSchema|JsonSchema[]
     */
    public function getItems()
    {
        return $this->items;
    }
    /**
     * @param JsonSchema|JsonSchema[] $items
     *
     * @return self
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
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
     *
     * @return self
     */
    public function setMaxItems($maxItems)
    {
        $this->maxItems = $maxItems;

        return $this;
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
     *
     * @return self
     */
    public function setMinItems($minItems)
    {
        $this->minItems = $minItems;

        return $this;
    }
    /**
     * @return bool
     */
    public function getUniqueItems()
    {
        return $this->uniqueItems;
    }
    /**
     * @param bool $uniqueItems
     *
     * @return self
     */
    public function setUniqueItems($uniqueItems)
    {
        $this->uniqueItems = $uniqueItems;

        return $this;
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
     *
     * @return self
     */
    public function setMaxProperties($maxProperties)
    {
        $this->maxProperties = $maxProperties;

        return $this;
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
     *
     * @return self
     */
    public function setMinProperties($minProperties)
    {
        $this->minProperties = $minProperties;

        return $this;
    }
    /**
     * @return string[]
     */
    public function getRequired()
    {
        return $this->required;
    }
    /**
     * @param string[] $required
     *
     * @return self
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }
    /**
     * @return bool|JsonSchema
     */
    public function getAdditionalProperties()
    {
        return $this->additionalProperties;
    }
    /**
     * @param bool|JsonSchema $additionalProperties
     *
     * @return self
     */
    public function setAdditionalProperties($additionalProperties)
    {
        $this->additionalProperties = $additionalProperties;

        return $this;
    }
    /**
     * @return JsonSchema[]
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }
    /**
     * @param JsonSchema[] $definitions
     *
     * @return self
     */
    public function setDefinitions($definitions)
    {
        $this->definitions = $definitions;

        return $this;
    }
    /**
     * @return JsonSchema[]
     */
    public function getProperties()
    {
        return $this->properties;
    }
    /**
     * @param JsonSchema[] $properties
     *
     * @return self
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;

        return $this;
    }
    /**
     * @return JsonSchema[]
     */
    public function getPatternProperties()
    {
        return $this->patternProperties;
    }
    /**
     * @param JsonSchema[] $patternProperties
     *
     * @return self
     */
    public function setPatternProperties($patternProperties)
    {
        $this->patternProperties = $patternProperties;

        return $this;
    }
    /**
     * @return JsonSchema[]|string[][]
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }
    /**
     * @param JsonSchema[]|string[][] $dependencies
     *
     * @return self
     */
    public function setDependencies($dependencies)
    {
        $this->dependencies = $dependencies;

        return $this;
    }
    /**
     * @return mixed[]
     */
    public function getEnum()
    {
        return $this->enum;
    }
    /**
     * @param mixed[] $enum
     *
     * @return self
     */
    public function setEnum($enum)
    {
        $this->enum = $enum;

        return $this;
    }
    /**
     * @return mixed|mixed[]
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @param mixed|mixed[] $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
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
     *
     * @return self
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }
    /**
     * @return JsonSchema[]
     */
    public function getAllOf()
    {
        return $this->allOf;
    }
    /**
     * @param JsonSchema[] $allOf
     *
     * @return self
     */
    public function setAllOf($allOf)
    {
        $this->allOf = $allOf;

        return $this;
    }
    /**
     * @return JsonSchema[]
     */
    public function getAnyOf()
    {
        return $this->anyOf;
    }
    /**
     * @param JsonSchema[] $anyOf
     *
     * @return self
     */
    public function setAnyOf($anyOf)
    {
        $this->anyOf = $anyOf;

        return $this;
    }
    /**
     * @return JsonSchema[]
     */
    public function getOneOf()
    {
        return $this->oneOf;
    }
    /**
     * @param JsonSchema[] $oneOf
     *
     * @return self
     */
    public function setOneOf($oneOf)
    {
        $this->oneOf = $oneOf;

        return $this;
    }
    /**
     * @return JsonSchema
     */
    public function getNot()
    {
        return $this->not;
    }
    /**
     * @param JsonSchema $not
     *
     * @return self
     */
    public function setNot($not)
    {
        $this->not = $not;

        return $this;
    }
}
