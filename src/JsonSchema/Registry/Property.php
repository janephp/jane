<?php

namespace Joli\Jane\JsonSchema\Registry;

use Symfony\Component\PropertyInfo\Type;

class Property
{
    use OriginAwareTrait;

    /** @var string */
    private $name;

    /** @var bool */
    private $isReadable;

    /** @var bool */
    private $isWritable;

    /** @var string */
    private $shortDescription;

    /** @var string */
    private $longDescription;

    /** @var Type[] */
    private $types = [];

    /**
     * @param string $name
     * @param bool $isReadable
     * @param bool $isWritable
     * @param string $shortDescription
     * @param string $longDescription
     */
    public function __construct($origin, $name, $isReadable, $isWritable, $shortDescription, $longDescription)
    {
        $this->origin = $origin;
        $this->name = $name;
        $this->isReadable = $isReadable;
        $this->isWritable = $isWritable;
        $this->shortDescription = $shortDescription;
        $this->longDescription = $longDescription;
    }

    /**
     * @param Type $type
     */
    public function addType(Type $type)
    {
        $this->types[] = $type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isReadable()
    {
        return $this->isReadable;
    }

    /**
     * @return bool
     */
    public function isWritable()
    {
        return $this->isWritable;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @return string
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * @return Type[]
     */
    public function getTypes()
    {
        return $this->types;
    }
}
