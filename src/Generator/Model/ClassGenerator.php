<?php

namespace Joli\Jane\Generator\Model;

use Joli\Jane\Generator\Naming;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

trait ClassGenerator
{
    /**
     * The naming service
     *
     * @return Naming
     */
    abstract protected function getNaming();

    /**
     * Return a model class
     *
     * @param string   $name
     * @param Node[]   $properties
     * @param Node[]   $methods
     * @param string[] $types
     *
     * @return Stmt\Class_
     */
    protected function createModel($name, $properties, $methods, $types)
    {
        $implements = [];

        foreach ($types as $type) {
            $implements[] = new Name($this->getNaming()->getInterfaceName($type));
        }

        $implements[] = new Name($this->getNaming()->getInterfaceName($name));

        return new Stmt\Class_(
            new Name($this->getNaming()->getClassName($name)),
            [
                'stmts'      => array_merge($properties, $methods),
                'implements' => $implements,
            ]
        );
    }
}
