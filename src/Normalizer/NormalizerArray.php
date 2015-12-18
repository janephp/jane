<?php

namespace Joli\Jane\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class NormalizerArray extends SerializerAwareNormalizer implements DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if ($this->serializer === null) {
            throw new \BadMethodCallException('Please set a serializer before calling denormalize()!');
        }

        if (!is_array($data)) {
            throw new \InvalidArgumentException('Data expected to be an array, '.gettype($data).' given.');
        }

        if (substr($class, -2) !== '[]') {
            throw new \InvalidArgumentException('Unsupported class: '.$class);
        }

        $serializer = $this->serializer;
        $class = substr($class, 0, -2);

        return array_map(
            function ($data) use ($serializer, $class, $format, $context) {
                return $serializer->denormalize($data, $class, $format, $context);
            },
            $data
        );
    }
    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return substr($type, -2) === '[]' && $this->serializer->supportsDenormalization($data, substr($type, 0, -2), $format);
    }
}
