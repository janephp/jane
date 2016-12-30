<?php

declare(strict_types = 1);

namespace Joli\Jane\AstGenerator\Writer;

use PhpParser\Node;

interface WriterInterface
{
    /**
     * Write a list of nodes
     *
     * @param Node[] $nodes
     *
     * @return mixed
     */
    public function write($nodes);
}
