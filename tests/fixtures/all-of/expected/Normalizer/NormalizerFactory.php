<?php

namespace Joli\Jane\Tests\Expected\Normalizer;

class NormalizerFactory
{
    public static function create()
    {
        $normalizers   = [];
        $normalizers[] = new \Joli\Jane\Runtime\Normalizer\ArrayDenormalizer();
        $normalizers[] = new TestNormalizer();
        $normalizers[] = new ChildtypeNormalizer();
        $normalizers[] = new ParenttypeNormalizer();

        return $normalizers;
    }
}
