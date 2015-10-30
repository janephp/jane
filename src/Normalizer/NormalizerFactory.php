<?php

namespace Joli\Jane\Normalizer;

class NormalizerFactory
{
    public static function create()
    {
        $normalizers   = [];
        $normalizers[] = new ReferenceNormalizer();
        $normalizers[] = new NormalizerArray();
        $normalizers[] = new JsonSchemaNormalizer();

        return $normalizers;
    }
}
