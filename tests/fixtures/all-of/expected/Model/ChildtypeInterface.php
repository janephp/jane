<?php

namespace Joli\Jane\Tests\Expected\Model;

interface ChildtypeInterface
{
    /**
     * @return string
     */
    public function getChildProperty();

    /**
     * @param string $childProperty
     *
     * @return self
     */
    public function setChildProperty($childProperty = null);

    /**
     * @return string
     */
    public function getInheritedProperty();

    /**
     * @param string $inheritedProperty
     *
     * @return self
     */
    public function setInheritedProperty($inheritedProperty = null);
}
