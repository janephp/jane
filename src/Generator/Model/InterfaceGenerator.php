<?php

namespace Joli\Jane\Generator\Model;

use Joli\Jane\Generator\Naming;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\BuilderFactory;

trait InterfaceGenerator
{
    /**
     * The naming service
     *
     * @return Naming
     */
    abstract protected function getNaming();

    /**
     * Return a interface for the model class
     *
     * @param string             $name
     * @param Stmt\ClassMethod[] $methods
     *
     * @return Stmt\Interface_
     */
    protected function createModelInterface($name, $methods)
    {
        $buildFactory     = new BuilderFactory();
        $interfaceBuilder = $buildFactory->interface($this->getNaming()->getInterfaceName($name));

        foreach ($methods as $method) {
            $abstractMethod = $buildFactory->method($method->name)
                                           ->addParams($method->params)
                                           ->setDocComment($method->getDocComment());

            if ($method->getReturnType()) {
                $abstractMethod = $abstractMethod->setReturnType($method->getReturnType());
            }

            $interfaceBuilder->addStmt($abstractMethod);
        }

        $interface = $interfaceBuilder->getNode();

        return $interface;
    }
}
