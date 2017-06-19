<?php

namespace Joli\Jane\Tests\Expected\Normalizer;

use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class TestNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Joli\\Jane\\Tests\\Expected\\Model\\Test') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Joli\Jane\Tests\Expected\Model\Test) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (!is_object($data)) {
            throw new InvalidArgumentException();
        }
        $object = new \Joli\Jane\Tests\Expected\Model\Test();
        if (property_exists($data, 'child')) {
            $object->setChild($this->serializer->deserialize($data->{'child'}, 'Joli\\Jane\\Tests\\Expected\\Model\\Childtype', 'raw', $context));
        }
        if (property_exists($data, 'parent')) {
            $object->setParent($this->serializer->deserialize($data->{'parent'}, 'Joli\\Jane\\Tests\\Expected\\Model\\Parenttype', 'raw', $context));
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getChild()) {
            $data->{'child'} = $this->serializer->serialize($object->getChild(), 'raw', $context);
        }
        if (null !== $object->getParent()) {
            $data->{'parent'} = $this->serializer->serialize($object->getParent(), 'raw', $context);
        }

        return $data;
    }
}
