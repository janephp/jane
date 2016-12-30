<?php

declare(strict_types = 1);

namespace Joli\Jane\AstGenerator\Generator\Hydrate;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class ObjectFromArray extends Object
{
    /**
     * {@inheritdoc}
     */
    protected function createInputExpr(Expr\Variable $inputVariable, $property)
    {
        return new Expr\ArrayDimFetch($inputVariable, new Scalar\String_($property));
    }
}
