<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;

class StringType extends SimpleType
{
    /**
     * {@inheritDoc}
     */
    public function getSimpleType()
    {
        return 'string';
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpTypes($schema, $name, Context $context)
    {
        return ['string'];
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationIfStmt($schema, $name, Context $context, Expr $input)
    {
        return new Expr\FuncCall(new Name('is_string'), [new Arg($input)]);
    }
}
