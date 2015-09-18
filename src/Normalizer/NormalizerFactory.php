<?php

namespace Joli\Jane\Normalizer;

class NormalizerFactory
{
    public static function create()
    {
        $normalizers = array();
        $normalizers[] = new JsonSchemaNormalizer();

        return $normalizers;
    }
}
