<?php

namespace Joli\Jane\Generator\Normalizer;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;

trait NormalizerGenerator
{
    /**
     * @var \Joli\Jane\Generator\Naming
     */
    protected $naming;

    protected function createNormalizerClass($name, $methods)
    {
        return new Stmt\Class_(
            new Name($this->naming->getClassName($name)),
            [
                'stmts' => array_merge($methods),
                'implements' => [new Name('DenormalizerInterface')],
                'extends'    => new Name('SerializerAwareNormalizer')
            ]
        );
    }

    protected function createNormalizeMethod()
    {
    }
}
 