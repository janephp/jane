<?php

declare(strict_types = 1);

namespace Joli\Jane\AstGenerator\Generator\Hydrate;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class ToArrayFromObject extends FromObject
{
    /**
     * {@inheritdoc}
     */
    protected function getAssignStatement($dataVariable)
    {
        return new Expr\Assign($dataVariable, new Expr\Array_());
    }

    /**
     * {@inheritdoc}
     */
    protected function getSubAssignVariableStatement($dataVariable, $property)
    {
        return new Expr\ArrayDimFetch($dataVariable, new Scalar\String_($property));
    }
}
