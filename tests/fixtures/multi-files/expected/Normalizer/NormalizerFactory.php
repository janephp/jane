<?php

namespace Joli\Jane\Tests\Expected\Normalizer;

class NormalizerFactory
{
    public static function create()
    {
        $normalizers   = [];
        $normalizers[] = new TestNormalizer();
        $normalizers[] = new TestfooNormalizer();

        return $normalizers;
    }
}
