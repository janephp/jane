<?php

namespace Joli\Jane\JsonSchema\Registry;

class Model
{
    use OriginAwareTrait;

    /** @var Property[] */
    private $properties = [];

    /** @var string  */
    private $reference;

    /** @var string  */
    private $name;

    /**
     * @param mixed  $origin
     * @param string $name
     * @param string $reference
     */
    public function __construct($origin, $name, $reference)
    {
        $this->origin = $origin;
        $this->name = $name;
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param Property $property
     */
    public function addProperty(Property $property)
    {
        $this->properties[$property->getName()] = $property;
    }

    /**
     * @param string $property
     *
     * @return Property
     */
    public function getProperty($property)
    {
        return $this->properties[$property];
    }

    /**
     * @return string[]
     */
    public function getProperties()
    {
        return array_keys($this->properties);
    }
}
