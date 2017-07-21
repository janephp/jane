<?php

namespace Joli\Jane\Tests\Expected\Schema2\Normalizer;

class NormalizerFactory
{
    public static function create()
    {
        $normalizers   = [];
        $normalizers[] = new \Symfony\Component\Serializer\Normalizer\ArrayDenormalizer();
        $normalizers[] = new FooNormalizer();

        return $normalizers;
    }
}
