<?php

namespace Joli\Jane\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class NormalizerArray extends SerializerAwareNormalizer implements DenormalizerInterface
{
    /**
     * {@inheritDoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        preg_match('/array<(.+?)>/', $class, $matches);
        $subClass = $matches[1];

        $collection = [];

        foreach ($data as $item) {
            $collection[] = $this->serializer->deserialize($item, $subClass, 'raw');
        }

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return preg_match('/array<(.+?)>/', $type);
    }
}
 