<?php

namespace Joli\Jane\Tests\Expected\Normalizer;

class NormalizerFactory
{
    public static function create()
    {
        $normalizers   = [];
        $normalizers[] = new \Symfony\Component\Serializer\Normalizer\ArrayDenormalizer();
        $normalizers[] = new TestNormalizer();
        $normalizers[] = new OtherchildtypeNormalizer();
        $normalizers[] = new ChildtypeNormalizer();
        $normalizers[] = new ParenttypeNormalizer();

        return $normalizers;
    }
}
