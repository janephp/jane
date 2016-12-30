<?php

namespace Joli\Jane\JsonReference;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ReferenceNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * {@inheritDoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $ref = new \stdClass();
        $ref->{'$ref'} = (string) $object->getReferenceUri();

        return $ref;
    }

    /**
     * {@inheritDoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (!array_key_exists('document-origin', $context)) {
            throw new \LogicException('Context should have a document-origin key containing the uri of the current json document');
        }

        return new Reference($data->{'$ref'}, $context['document-origin']);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return ($data instanceof Reference);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return (
            $data instanceof \stdClass
            &&
            property_exists($data, '$ref')
        );
    }
}
