<?php

namespace Joli\Jane\Generator\Context;

use PhpParser\Node\Stmt\Class_;

class ObjectClassMap implements \ArrayAccess
{
    /**
     * @var Class_[] A list of class
     * @key string   Key is an object converted to string using spl object hash
     */
    private $references;

    /**
     * {@inheritDoc}
     */
    public function offsetExists($object)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException("The key must be an object");
        }

        return array_key_exists(spl_object_hash($object), $this->references);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($object)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException("The key must be an object");
        }

        return $this->references[spl_object_hash($object)];
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($object, $class)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException("The key must be an object");
        }

        if (!($class instanceof Class_)) {
            throw new \InvalidArgumentException("The value must be an instance of PhpParser\\Node\\Stmt\\Class_");
        }

        $this->references[spl_object_hash($object)] = $class;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($object)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException("The key must be an object");
        }

        unset($this->references[spl_object_hash($object)]);
    }
}
