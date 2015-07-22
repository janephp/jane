<?php

namespace Joli\Jane\Generator;

use Joli\Jane\Schema\Schema;

interface GeneratorInterface
{
    /**
     * Generate a set of files given a schema
     *
     * @param Schema $rootSchema     Root schema to generate from
     * @param Schema $schema         Schema to generate from
     * @param string $className      Class to generate
     * @param string $classNamespace Namespace of class to generate
     * @param string $directory      Directory where files are generated
     *
     * @return \Memio\Model\File[]
     */
    public function generate(Schema $rootSchema, Schema $schema, $className, $classNamespace, $directory);
}
 