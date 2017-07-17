<?php

namespace Joli\Jane\Normalizer;

class NormalizerFactory
{
    public static function create()
    {
        $normalizers   = [];
        $normalizers[] = new \Symfony\Component\Serializer\Normalizer\ArrayDenormalizer();
        $normalizers[] = new \Joli\Jane\Runtime\Normalizer\ReferenceNormalizer();
        $normalizers[] = new JsonSchemaNormalizer();

        return $normalizers;
    }
}
