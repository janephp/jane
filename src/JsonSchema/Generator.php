<?php

declare(strict_types=1);

namespace Joli\Jane\JsonSchema;

use Joli\Jane\AstGenerator\Generator\SchemaGenerator;
use Joli\Jane\AstGenerator\Writer\WriterInterface;
use Joli\Jane\JsonSchema\Registry\Schema;

class Generator
{
    /** @var SchemaGenerator  */
    private $schemaGenerator;

    /** @var WriterInterface  */
    private $writer;

    public function __construct(SchemaGenerator $schemaGenerator, WriterInterface $writer)
    {
        $this->schemaGenerator = $schemaGenerator;
        $this->writer = $writer;
    }

    /**
     * @param Schema[] $schemas
     */
    public function generate($schemas)
    {
        $nodes = [];

        foreach ($schemas as $schema) {
            $nodes = array_merge($nodes, $this->schemaGenerator->generate($schema->getName()));
        }

        $this->writer->write($nodes);
    }
}
