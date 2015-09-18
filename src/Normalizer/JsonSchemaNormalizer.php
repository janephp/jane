<?php

namespace Joli\Jane\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class JsonSchemaNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Joli\\Jane\\Model\\JsonSchema') {
            return false;
        }

        return true;
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
}
