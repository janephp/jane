<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;

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
    public function getDenormalizationIfStmt($schema, $name, Context $context, Expr $input)
    {
        return new Expr\FuncCall(new Name('is_null'), [new Arg($input)]);
    }
}
