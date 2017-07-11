<?php

namespace Joli\Jane\Tests\Expected\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class ChildtypeNormalizer implements DenormalizerInterface, NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Joli\\Jane\\Tests\\Expected\\Model\\Childtype') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Joli\Jane\Tests\Expected\Model\Childtype) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $object = new \Joli\Jane\Tests\Expected\Model\Childtype();
        if (property_exists($data, 'childProperty')) {
            $object->setChildProperty($data->{'childProperty'});
        }
        if (property_exists($data, 'inheritedProperty')) {
            $object->setInheritedProperty($data->{'inheritedProperty'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getChildProperty()) {
            $data->{'childProperty'} = $object->getChildProperty();
        }
        if (null !== $object->getInheritedProperty()) {
            $data->{'inheritedProperty'} = $object->getInheritedProperty();
        }

        return $data;
    }
}
