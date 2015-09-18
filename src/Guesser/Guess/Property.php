<?php

namespace Joli\Jane\Guesser\Guess;

class Property
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Type
     */
    private $type;

    /**
     * @var mixed
     */
    private $object;

    public function __construct($object, $name, $type = null, $options = array())
    {
        $this->name   = $name;
        $this->object = $object;
        $this->type   = $type;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Return name of the property
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return type of the property
     *
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type
     *
     * @param Type $type
     */
    public function setType(Type $type)
    {
        $this->type = $type;
    }
}
 