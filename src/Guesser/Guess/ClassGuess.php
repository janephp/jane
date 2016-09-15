<?php

namespace Joli\Jane\Guesser\Guess;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class ClassGuess
{
    /**
     * @var string Name of the class
     */
    private $name;

    /**
     * @var string[] Types that this class is composed of
     */
    private $types;

    /**
     * @var mixed Object link to the generation
     */
    private $object;

    /**
     * @var Property[]
     */
    private $properties;

    /**
     * ClassGuess constructor.
     *
     * @param mixed    $object
     * @param string   $name
     * @param string[] $types
     */
    public function __construct($object, $name, $types = [])
    {
        $this->name   = $name;
        $this->object = $object;
        $this->types  = $types;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Property[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param Property[] $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return \string[]
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param \string[] $types
     */
    public function setTypes($types)
    {
        $this->types = $types;
    }
}
