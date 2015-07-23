<?php

namespace Joli\Jane\Generator;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Schema\Schema;

interface GeneratorInterface
{
    /**
     * Generate a set of files given a schema
     *
     * @param Schema  $schema     Schema to generate from
     * @param string  $className  Class to generate
     * @param Context $context    Context for generation
     *
     * @return \Memio\Model\File[]
     */
    public function generate(Schema $schema, $className, Context $context);
}
 