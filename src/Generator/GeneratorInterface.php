<?php

namespace Joli\Jane\Generator;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Model\JsonSchema;

interface GeneratorInterface
{
    /**
     * Generate a set of files given a schema
     *
     * @param JsonSchema  $schema     Schema to generate from
     * @param string  $className  Class to generate
     * @param Context $context    Context for generation
     *
     * @return File[]
     */
    public function generate($schema, $className, Context $context);
}
