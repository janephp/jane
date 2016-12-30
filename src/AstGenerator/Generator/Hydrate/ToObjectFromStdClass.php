<?php

declare(strict_types = 1);

namespace Joli\Jane\AstGenerator\Generator\Hydrate;

use PhpParser\Node\Expr;

class ObjectFromStdClass extends Object
{
    /**
     * {@inheritdoc}
     */
    protected function createInputExpr(Expr\Variable $inputVariable, $property)
    {
        return new Expr\PropertyFetch($inputVariable, sprintf("{'%s'}", $property));
    }
}
