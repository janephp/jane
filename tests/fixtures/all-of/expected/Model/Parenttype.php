<?php

namespace Joli\Jane\Tests\Expected\Model;

class Parenttype implements ParenttypeInterface
{
    /**
     * @var string
     */
    protected $inheritedProperty;

    /**
     * @return string
     */
    public function getInheritedProperty()
    {
        return $this->inheritedProperty;
    }

    /**
     * @param string $inheritedProperty
     *
     * @return self
     */
    public function setInheritedProperty($inheritedProperty = null)
    {
        $this->inheritedProperty = $inheritedProperty;

        return $this;
    }
}
