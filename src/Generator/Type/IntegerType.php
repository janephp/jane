<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;

class IntegerType extends SimpleType
{
    /**
     * {@inheritDoc}
     */
    public function getSimpleType()
    {
        return 'integer';
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpTypes($schema, $name, Context $context)
    {
        return ['int'];
    }

    /**
     * {@inheritDoc}
     */
    public function getRawCheck($schema, $name, Context $context)
    {
        return 'is_int(%s)';
    }
}
