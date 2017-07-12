<?php

namespace Joli\Jane\Tests\Expected\Schema1\Normalizer;

class NormalizerFactory
{
    public static function create()
    {
        $normalizers   = [];
        $normalizers[] = new TestNormalizer();

        return $normalizers;
    }
}
