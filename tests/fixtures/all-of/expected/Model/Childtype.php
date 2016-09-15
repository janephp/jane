<?php

namespace Joli\Jane\Tests\Expected\Model;

class Childtype
{
    /**
     * @var string
     */
    protected $childProperty;
    /**
     * @var string
     */
    protected $inheritedProperty;

    /**
     * @return string
     */
    public function getChildProperty()
    {
        return $this->childProperty;
    }

    /**
     * @param string $childProperty
     *
     * @return self
     */
    public function setChildProperty($childProperty = null)
    {
        $this->childProperty = $childProperty;

        return $this;
    }

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
