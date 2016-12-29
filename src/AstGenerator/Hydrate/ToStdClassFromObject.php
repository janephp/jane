<?php

declare(strict_types = 1);

namespace Joli\Jane\AstGenerator\Hydrate;

use PhpParser\Node\Name;
use PhpParser\Node\Expr;

/**
 * Create AST Statement to normalize a Class into a stdClassObject.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class ToStdClassFromObject extends FromObject
{
    /**
     * {@inheritdoc}
     */
    protected function getAssignStatement($dataVariable)
    {
        return new Expr\Assign($dataVariable, new Expr\New_(new Name('\\stdClass')));
    }

    /**
     * {@inheritdoc}
     */
    protected function getSubAssignVariableStatement($dataVariable, $property)
    {
        return new Expr\PropertyFetch($dataVariable, sprintf("{'%s'}", $property));
    }
}
