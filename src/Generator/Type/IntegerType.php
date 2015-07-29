<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;

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
    public function getDenormalizationIfStmt($schema, $name, Context $context, Expr $input)
    {
        return new Expr\FuncCall(new Name('is_array'), [new Arg($input)]);
    }
}
