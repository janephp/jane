<?php

declare(strict_types=1);

namespace Joli\Jane\AstGenerator\Generator;

use Joli\Jane\AstGenerator\Generator\Exception\NotSupportedGeneratorException;

/**
 * An AstGeneratorInterface is a contract to transform an object into an AST.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
interface AstGeneratorInterface
{
    /**
     * Generate an object into an AST given a specific context.
     *
     * @param mixed $object  Object to generate AST from
     * @param array $context Context for the generator
     *
     * @throws NotSupportedGeneratorException When no node can be generated
     *
     * @return \PhpParser\Node[] An array of statements (AST Node)
     */
    public function generate($object, array $context = array());
}
