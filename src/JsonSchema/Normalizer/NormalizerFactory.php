<?php

namespace Joli\Jane\Normalizer;

class NormalizerFactory
{
    public static function create()
    {
        $normalizers   = [];
        $normalizers[] = new \Joli\Jane\JsonReference\ReferenceNormalizer();
        $normalizers[] = new JsonSchemaNormalizer();

        return $normalizers;
    }
}
