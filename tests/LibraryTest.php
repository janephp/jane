<?php

namespace Joli\Jane\Tests;

use Joli\Jane\Jane;

class LibraryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Jane
     */
    protected $jane;

    public function setUp()
    {
        $this->jane = Jane::build();
    }

    /**
     * Unique test with ~100% coverage, library generated from json schema must be the same as the library used
     */
    public function testLibrary()
    {
        $this->jane->generate(__DIR__ . '/data/json-schema.json', 'JsonSchema', 'Joli\\Jane', __DIR__ . "/generated");

        $this->assertTrue(file_exists(__DIR__ . "/generated/Model/JsonSchema.php"));
        $this->assertTrue(file_exists(__DIR__ . "/generated/Normalizer/JsonSchemaNormalizer.php"));
        $this->assertTrue(file_exists(__DIR__ . "/generated/Normalizer/NormalizerChain.php"));

        $this->assertEquals(
            file_get_contents(__DIR__ . "/../src/Model/JsonSchema.php"),
            file_get_contents(__DIR__ . "/generated/Model/JsonSchema.php")
        );

        $this->assertEquals(
            file_get_contents(__DIR__ . "/../src/Normalizer/JsonSchemaNormalizer.php"),
            file_get_contents(__DIR__ . "/generated/Normalizer/JsonSchemaNormalizer.php")
        );

        $this->assertEquals(
            file_get_contents(__DIR__ . "/../src/Normalizer/NormalizerChain.php"),
            file_get_contents(__DIR__ . "/generated/Normalizer/NormalizerChain.php")
        );
    }
} 
