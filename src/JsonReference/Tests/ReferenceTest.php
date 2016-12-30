<?php

namespace Joli\Jane\JsonReference\Tests;

use Joli\Jane\JsonReference\Reference;

class ReferenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider resolveProvider
     */
    public function testResolve($reference, $origin, $expected, $denormalizerCallback)
    {
        $reference = new Reference($reference, $origin);

        self::assertEquals($expected, $reference->resolve($denormalizerCallback));
    }

    public function resolveProvider()
    {
        $schemaPath = str_replace(getcwd() . DIRECTORY_SEPARATOR, '', __DIR__ . '/schema.json');

        return [
            ['#', $schemaPath, json_decode(file_get_contents($schemaPath)), null],
            [
                'https://raw.githubusercontent.com/json-schema/json-schema/master/draft-04/schema#/id',
                $schemaPath,
                'http://json-schema.org/draft-04/schema#',
                null
            ]
        ];
    }
}
