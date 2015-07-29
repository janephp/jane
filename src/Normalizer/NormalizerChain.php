<?php

namespace Joli\Jane\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class NormalizerChain implements DenormalizerInterface
{
    private $normalizers = array();
    public function addNormalizer($normalizer)
    {
        $normalizer->setNormalizerChain($this);
        $this->normalizers[] = $normalizer;
    }
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->supportsDenormalization($data, $class, $format)) {
                return $normalizer->denormalize($data, $class, $format, $context);
            }
        }

        return;
    }
    public function supportsDenormalization($data, $type, $format = null)
    {
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->supportsDenormalization($data, $type, $format)) {
                return true;
            }
        }

        return false;
    }
    public static function build()
    {
        $normalizer = new self();
        $normalizer->addNormalizer(new JsonSchemaNormalizer());

        return $normalizer;
    }
}
