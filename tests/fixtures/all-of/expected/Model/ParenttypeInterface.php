<?php

namespace Joli\Jane\Tests\Expected\Model;

interface ParenttypeInterface
{
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
