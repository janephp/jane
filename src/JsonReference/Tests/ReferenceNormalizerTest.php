<?php

namespace Joli\Jane\JsonReference\Tests;

use Joli\Jane\JsonReference\Reference;
use Joli\Jane\JsonReference\ReferenceNormalizer;

class ReferenceNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ReferenceNormalizer */
    private $referenceNormalizer;

    public function setUp()
    {
        $this->referenceNormalizer = new ReferenceNormalizer();
    }

    /**
     * @dataProvider referenceProvider
     */
    public function testNormalize($referenceString)
    {
        $reference = new Reference($referenceString, 'schema.json');
        $normalized = $this->referenceNormalizer->normalize($reference);

        $this->assertEquals($referenceString, $normalized->{'$ref'});
    }

    public function testSupportsNormalize()
    {
        $this->assertFalse($this->referenceNormalizer->supportsNormalization('toto'));
        $this->assertTrue($this->referenceNormalizer->supportsNormalization(new Reference('reference', 'schema.json')));
    }

    /**
     * @dataProvider referenceProvider
     */
    public function testDenormalize($referenceString)
    {
        $refObject = new \stdClass();
        $refObject->{'$ref'} = $referenceString;
        $reference = $this->referenceNormalizer->denormalize($refObject, null, 'json', ['document-origin' => 'schema.json']);

        $this->assertInstanceOf(Reference::class, $reference);
    }

    public function referenceProvider()
    {
        return [
            ['#pointer'],
            ['#'],
            ['https://my-site/schema#pointer'],
            ['my-site.com/teest']
        ];
    }
}
