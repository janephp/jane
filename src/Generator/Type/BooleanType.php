<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;

class BooleanType extends SimpleType
{
    /**
     * {@inheritDoc}
     */
    public function getSimpleType()
    {
        return 'boolean';
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpTypes($schema, $name, Context $context)
    {
        return ['bool'];
    }

    /**
     * {@inheritDoc}
     */
    public function getRawCheck($schema, $name, Context $context)
    {
        return 'is_bool(%s)';
    }
}
