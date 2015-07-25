<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;

class NullType extends SimpleType
{
    /**
     * {@inheritDoc}
     */
    public function getSimpleType()
    {
        return 'null';
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpTypes($schema, $name, Context $context)
    {
        return ['null'];
    }

    /**
     * {@inheritDoc}
     */
    public function getRawCheck($schema, $name, Context $context)
    {
        return 'is_null(%s)';
    }
}
 