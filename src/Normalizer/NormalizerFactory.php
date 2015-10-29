<?php

namespace Joli\Jane\Normalizer;

use Joli\Jane\Normalizer\ReferenceNormalizer;
use Joli\Jane\Normalizer\NormalizerArray;

class NormalizerFactory
{
    public static function create()
    {
        $normalizers = array();
        $normalizers[] = new ReferenceNormalizer();
        $normalizers[] = new NormalizerArray();
        $normalizers[] = new JsonSchemaNormalizer();

        return $normalizers;
    }
}
