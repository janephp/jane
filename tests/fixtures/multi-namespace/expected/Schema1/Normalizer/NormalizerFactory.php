<?php

namespace Joli\Jane\Tests\Expected\Schema1\Normalizer;

class NormalizerFactory
{
    public static function create()
    {
        $normalizers   = [];
        $normalizers[] = new \Joli\Jane\Runtime\Normalizer\ArrayDenormalizer();
        $normalizers[] = new TestNormalizer();
        $normalizers[] = new \Joli\Jane\Tests\Expected\Schema2\Normalizer\FooNormalizer();

        return $normalizers;
    }
}
