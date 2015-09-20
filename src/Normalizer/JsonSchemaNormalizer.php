<?php

namespace Joli\Jane\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class JsonSchemaNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Joli\\Jane\\Model\\JsonSchema') {
            return false;
        }

        return true;
    }
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Joli\Jane\Model\JsonSchema) {
            return true;
        }

        return false;
    }
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (empty($data)) {
            return null;
        }
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Joli\Jane\Model\JsonSchema();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'id'})) {
            $object->setId($data->{'id'});
        }
        if (isset($data->{'$schema'})) {
            $object->setDollarSchema($data->{'$schema'});
        }
        if (isset($data->{'title'})) {
            $object->setTitle($data->{'title'});
        }
        if (isset($data->{'description'})) {
            $object->setDescription($data->{'description'});
        }
        if (isset($data->{'default'})) {
            $object->setDefault($data->{'default'});
        }
        if (isset($data->{'multipleOf'})) {
            $object->setMultipleOf($data->{'multipleOf'});
        }
        if (isset($data->{'maximum'})) {
            $object->setMaximum($data->{'maximum'});
        }
        if (isset($data->{'exclusiveMaximum'})) {
            $object->setExclusiveMaximum($data->{'exclusiveMaximum'});
        }
        if (isset($data->{'minimum'})) {
            $object->setMinimum($data->{'minimum'});
        }
        if (isset($data->{'exclusiveMinimum'})) {
            $object->setExclusiveMinimum($data->{'exclusiveMinimum'});
        }
        if (isset($data->{'maxLength'})) {
            $object->setMaxLength($data->{'maxLength'});
        }
        if (isset($data->{'minLength'})) {
            $object->setMinLength($data->{'minLength'});
        }
        if (isset($data->{'pattern'})) {
            $object->setPattern($data->{'pattern'});
        }
        if (isset($data->{'additionalItems'})) {
            $value = $data->{'additionalItems'};
            if (is_bool($data->{'additionalItems'})) {
                $value = $data->{'additionalItems'};
            }
            if (is_object($data->{'additionalItems'})) {
                $value = $this->serializer->deserialize($data->{'additionalItems'}, 'Joli\\Jane\\Model\\JsonSchema', 'raw', $context);
            }
            $object->setAdditionalItems($value);
        }
        if (isset($data->{'items'})) {
            $value_0 = $data->{'items'};
            if (is_object($data->{'items'})) {
                $value_0 = $this->serializer->deserialize($data->{'items'}, 'Joli\\Jane\\Model\\JsonSchema', 'raw', $context);
            }
            if (is_array($data->{'items'})) {
                $values = array();
                foreach ($data->{'items'} as $value_1) {
                    $values[] = $this->serializer->deserialize($value_1, 'Joli\\Jane\\Model\\JsonSchema', 'raw', $context);
                }
                $value_0 = $values;
            }
            $object->setItems($value_0);
        }
        if (isset($data->{'maxItems'})) {
            $object->setMaxItems($data->{'maxItems'});
        }
        if (isset($data->{'minItems'})) {
            $object->setMinItems($data->{'minItems'});
        }
        if (isset($data->{'uniqueItems'})) {
            $object->setUniqueItems($data->{'uniqueItems'});
        }
        if (isset($data->{'maxProperties'})) {
            $object->setMaxProperties($data->{'maxProperties'});
        }
        if (isset($data->{'minProperties'})) {
            $object->setMinProperties($data->{'minProperties'});
        }
        if (isset($data->{'required'})) {
            $values_2 = array();
            foreach ($data->{'required'} as $value_3) {
                $values_2[] = $value_3;
            }
            $object->setRequired($values_2);
        }
        if (isset($data->{'additionalProperties'})) {
            $value_4 = $data->{'additionalProperties'};
            if (is_bool($data->{'additionalProperties'})) {
                $value_4 = $data->{'additionalProperties'};
            }
            if (is_object($data->{'additionalProperties'})) {
                $value_4 = $this->serializer->deserialize($data->{'additionalProperties'}, 'Joli\\Jane\\Model\\JsonSchema', 'raw', $context);
            }
            $object->setAdditionalProperties($value_4);
        }
        if (isset($data->{'definitions'})) {
            $values_5 = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'definitions'} as $key => $value_6) {
                $values_5[$key] = $this->serializer->deserialize($value_6, 'Joli\\Jane\\Model\\JsonSchema', 'raw', $context);
            }
            $object->setDefinitions($values_5);
        }
        if (isset($data->{'properties'})) {
            $values_7 = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'properties'} as $key_9 => $value_8) {
                $values_7[$key_9] = $this->serializer->deserialize($value_8, 'Joli\\Jane\\Model\\JsonSchema', 'raw', $context);
            }
            $object->setProperties($values_7);
        }
        if (isset($data->{'patternProperties'})) {
            $values_10 = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'patternProperties'} as $key_12 => $value_11) {
                $values_10[$key_12] = $this->serializer->deserialize($value_11, 'Joli\\Jane\\Model\\JsonSchema', 'raw', $context);
            }
            $object->setPatternProperties($values_10);
        }
        if (isset($data->{'dependencies'})) {
            $values_13 = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'dependencies'} as $key_15 => $value_14) {
                $value_16 = $value_14;
                if (is_object($value_14)) {
                    $value_16 = $this->serializer->deserialize($value_14, 'Joli\\Jane\\Model\\JsonSchema', 'raw', $context);
                }
                if (is_array($value_14)) {
                    $values_17 = array();
                    foreach ($value_14 as $value_18) {
                        $values_17[] = $value_18;
                    }
                    $value_16 = $values_17;
                }
                $values_13[$key_15] = $value_16;
            }
            $object->setDependencies($values_13);
        }
        if (isset($data->{'enum'})) {
            $values_19 = array();
            foreach ($data->{'enum'} as $value_20) {
                $values_19[] = $value_20;
            }
            $object->setEnum($values_19);
        }
        if (isset($data->{'type'})) {
            $value_21 = $data->{'type'};
            if (isset($data->{'type'})) {
                $value_21 = $data->{'type'};
            }
            if (is_array($data->{'type'})) {
                $values_22 = array();
                foreach ($data->{'type'} as $value_23) {
                    $values_22[] = $value_23;
                }
                $value_21 = $values_22;
            }
            $object->setType($value_21);
        }
        if (isset($data->{'format'})) {
            $object->setFormat($data->{'format'});
        }
        if (isset($data->{'allOf'})) {
            $values_24 = array();
            foreach ($data->{'allOf'} as $value_25) {
                $values_24[] = $this->serializer->deserialize($value_25, 'Joli\\Jane\\Model\\JsonSchema', 'raw', $context);
            }
            $object->setAllOf($values_24);
        }
        if (isset($data->{'anyOf'})) {
            $values_26 = array();
            foreach ($data->{'anyOf'} as $value_27) {
                $values_26[] = $this->serializer->deserialize($value_27, 'Joli\\Jane\\Model\\JsonSchema', 'raw', $context);
            }
            $object->setAnyOf($values_26);
        }
        if (isset($data->{'oneOf'})) {
            $values_28 = array();
            foreach ($data->{'oneOf'} as $value_29) {
                $values_28[] = $this->serializer->deserialize($value_29, 'Joli\\Jane\\Model\\JsonSchema', 'raw', $context);
            }
            $object->setOneOf($values_28);
        }
        if (isset($data->{'not'})) {
            $object->setNot($this->serializer->deserialize($data->{'not'}, 'Joli\\Jane\\Model\\JsonSchema', 'raw', $context));
        }

        return $object;
    }
    public function normalize($object, $format = null, array $context = array())
    {
        $data = new \stdClass();
        if (null !== $object->getId()) {
            $data->{'id'} = $object->getId();
        }
        if (null !== $object->getDollarSchema()) {
            $data->{'$schema'} = $object->getDollarSchema();
        }
        if (null !== $object->getTitle()) {
            $data->{'title'} = $object->getTitle();
        }
        if (null !== $object->getDescription()) {
            $data->{'description'} = $object->getDescription();
        }
        if (null !== $object->getDefault()) {
            $data->{'default'} = $object->getDefault();
        }
        if (null !== $object->getMultipleOf()) {
            $data->{'multipleOf'} = $object->getMultipleOf();
        }
        if (null !== $object->getMaximum()) {
            $data->{'maximum'} = $object->getMaximum();
        }
        if (null !== $object->getExclusiveMaximum()) {
            $data->{'exclusiveMaximum'} = $object->getExclusiveMaximum();
        }
        if (null !== $object->getMinimum()) {
            $data->{'minimum'} = $object->getMinimum();
        }
        if (null !== $object->getExclusiveMinimum()) {
            $data->{'exclusiveMinimum'} = $object->getExclusiveMinimum();
        }
        if (null !== $object->getMaxLength()) {
            $data->{'maxLength'} = $object->getMaxLength();
        }
        if (null !== $object->getMinLength()) {
            $data->{'minLength'} = $object->getMinLength();
        }
        if (null !== $object->getPattern()) {
            $data->{'pattern'} = $object->getPattern();
        }
        if (null !== $object->getAdditionalItems()) {
            $value_30 = $object->getAdditionalItems();
            if (is_bool($object->getAdditionalItems())) {
                $value_30 = $object->getAdditionalItems();
            }
            if (is_object($object->getAdditionalItems())) {
                $value_30 = $this->serializer->serialize($object->getAdditionalItems(), 'raw', $context);
            }
            $data->{'additionalItems'} = $value_30;
        }
        if (null !== $object->getItems()) {
            $value_31 = $object->getItems();
            if (is_object($object->getItems())) {
                $value_31 = $this->serializer->serialize($object->getItems(), 'raw', $context);
            }
            if (is_array($object->getItems())) {
                $values_32 = array();
                foreach ($object->getItems() as $value_33) {
                    $values_32[] = $this->serializer->serialize($value_33, 'raw', $context);
                }
                $value_31 = $values_32;
            }
            $data->{'items'} = $value_31;
        }
        if (null !== $object->getMaxItems()) {
            $data->{'maxItems'} = $object->getMaxItems();
        }
        if (null !== $object->getMinItems()) {
            $data->{'minItems'} = $object->getMinItems();
        }
        if (null !== $object->getUniqueItems()) {
            $data->{'uniqueItems'} = $object->getUniqueItems();
        }
        if (null !== $object->getMaxProperties()) {
            $data->{'maxProperties'} = $object->getMaxProperties();
        }
        if (null !== $object->getMinProperties()) {
            $data->{'minProperties'} = $object->getMinProperties();
        }
        if (null !== $object->getRequired()) {
            $values_34 = array();
            foreach ($object->getRequired() as $value_35) {
                $values_34[] = $value_35;
            }
            $data->{'required'} = $values_34;
        }
        if (null !== $object->getAdditionalProperties()) {
            $value_36 = $object->getAdditionalProperties();
            if (is_bool($object->getAdditionalProperties())) {
                $value_36 = $object->getAdditionalProperties();
            }
            if (is_object($object->getAdditionalProperties())) {
                $value_36 = $this->serializer->serialize($object->getAdditionalProperties(), 'raw', $context);
            }
            $data->{'additionalProperties'} = $value_36;
        }
        if (null !== $object->getDefinitions()) {
            $values_37 = new \stdClass();
            foreach ($object->getDefinitions() as $key_39 => $value_38) {
                $values_37->{$key_39} = $this->serializer->serialize($value_38, 'raw', $context);
            }
            $data->{'definitions'} = $values_37;
        }
        if (null !== $object->getProperties()) {
            $values_40 = new \stdClass();
            foreach ($object->getProperties() as $key_42 => $value_41) {
                $values_40->{$key_42} = $this->serializer->serialize($value_41, 'raw', $context);
            }
            $data->{'properties'} = $values_40;
        }
        if (null !== $object->getPatternProperties()) {
            $values_43 = new \stdClass();
            foreach ($object->getPatternProperties() as $key_45 => $value_44) {
                $values_43->{$key_45} = $this->serializer->serialize($value_44, 'raw', $context);
            }
            $data->{'patternProperties'} = $values_43;
        }
        if (null !== $object->getDependencies()) {
            $values_46 = new \stdClass();
            foreach ($object->getDependencies() as $key_48 => $value_47) {
                $value_49 = $value_47;
                if (is_object($value_47)) {
                    $value_49 = $this->serializer->serialize($value_47, 'raw', $context);
                }
                if (is_array($value_47)) {
                    $values_50 = array();
                    foreach ($value_47 as $value_51) {
                        $values_50[] = $value_51;
                    }
                    $value_49 = $values_50;
                }
                $values_46->{$key_48} = $value_49;
            }
            $data->{'dependencies'} = $values_46;
        }
        if (null !== $object->getEnum()) {
            $values_52 = array();
            foreach ($object->getEnum() as $value_53) {
                $values_52[] = $value_53;
            }
            $data->{'enum'} = $values_52;
        }
        if (null !== $object->getType()) {
            $value_54 = $object->getType();
            if (!is_null($object->getType())) {
                $value_54 = $object->getType();
            }
            if (is_array($object->getType())) {
                $values_55 = array();
                foreach ($object->getType() as $value_56) {
                    $values_55[] = $value_56;
                }
                $value_54 = $values_55;
            }
            $data->{'type'} = $value_54;
        }
        if (null !== $object->getFormat()) {
            $data->{'format'} = $object->getFormat();
        }
        if (null !== $object->getAllOf()) {
            $values_57 = array();
            foreach ($object->getAllOf() as $value_58) {
                $values_57[] = $this->serializer->serialize($value_58, 'raw', $context);
            }
            $data->{'allOf'} = $values_57;
        }
        if (null !== $object->getAnyOf()) {
            $values_59 = array();
            foreach ($object->getAnyOf() as $value_60) {
                $values_59[] = $this->serializer->serialize($value_60, 'raw', $context);
            }
            $data->{'anyOf'} = $values_59;
        }
        if (null !== $object->getOneOf()) {
            $values_61 = array();
            foreach ($object->getOneOf() as $value_62) {
                $values_61[] = $this->serializer->serialize($value_62, 'raw', $context);
            }
            $data->{'oneOf'} = $values_61;
        }
        if (null !== $object->getNot()) {
            $data->{'not'} = $this->serializer->serialize($object->getNot(), 'raw', $context);
        }

        return $data;
    }
}
