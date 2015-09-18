<?php

namespace Joli\Jane\Generator\Model;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

trait ClassGenerator
{
    /**
     * @var \Joli\Jane\Generator\Naming
     */
    protected $naming;

    /**
     * Return a model class
     *
     * @param string $name
     * @param Node[] $properties
     * @param Node[] $methods
     *
     * @return Stmt\Class_
     */
    protected function createModel($name, $properties, $methods)
    {
        return new Stmt\Class_(
            new Name($this->naming->getClassName($name)),
            [
                'stmts' => array_merge($properties, $methods)
            ]
        );
    }
}
 