<?php

namespace Joli\Jane\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class JsonSchemaNormalizer implements DenormalizerInterface
{
    public $normalizerChain;
    public function setNormalizerChain(NormalizerChain $normalizerChain)
    {
        $this->normalizerChain = $normalizerChain;
    }
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Joli\\Jane\\Model\\JsonSchema') {
            return false;
        }
        if ($format !== 'json') {
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
            $value = null;
            if (is_int($data->{'minLength'})) {
                $value = $data->{'minLength'};
            }
            if (isset($data->{'minLength'})) {
                $value = $data->{'minLength'};
            }
            $object->setMinLength($value);
        }
        if (isset($data->{'pattern'})) {
            $object->setPattern($data->{'pattern'});
        }
        if (isset($data->{'additionalItems'})) {
            $value_0 = null;
            if (is_bool($data->{'additionalItems'})) {
                $value_0 = $data->{'additionalItems'};
            }
            if (is_object($data->{'additionalItems'})) {
                $value_0 = $this->normalizerChain->denormalize($data->{'additionalItems'}, 'Joli\\Jane\\Model\\JsonSchema', 'json', $context);
            }
            $object->setAdditionalItems($value_0);
        }
        if (isset($data->{'items'})) {
            $value_1 = null;
            if (is_object($data->{'items'})) {
                $value_1 = $this->normalizerChain->denormalize($data->{'items'}, 'Joli\\Jane\\Model\\JsonSchema', 'json', $context);
            }
            if (is_array($data->{'items'})) {
                $values = array();
                foreach ($data->{'items'} as $value_2) {
                    $values[] = $this->normalizerChain->denormalize($value_2, 'Joli\\Jane\\Model\\JsonSchema', 'json', $context);
                }
                $value_1 = $values;
            }
            $object->setItems($value_1);
        }
        if (isset($data->{'maxItems'})) {
            $object->setMaxItems($data->{'maxItems'});
        }
        if (isset($data->{'minItems'})) {
            $value_3 = null;
            if (is_int($data->{'minItems'})) {
                $value_3 = $data->{'minItems'};
            }
            if (isset($data->{'minItems'})) {
                $value_3 = $data->{'minItems'};
            }
            $object->setMinItems($value_3);
        }
        if (isset($data->{'uniqueItems'})) {
            $object->setUniqueItems($data->{'uniqueItems'});
        }
        if (isset($data->{'maxProperties'})) {
            $object->setMaxProperties($data->{'maxProperties'});
        }
        if (isset($data->{'minProperties'})) {
            $value_4 = null;
            if (is_int($data->{'minProperties'})) {
                $value_4 = $data->{'minProperties'};
            }
            if (isset($data->{'minProperties'})) {
                $value_4 = $data->{'minProperties'};
            }
            $object->setMinProperties($value_4);
        }
        if (isset($data->{'required'})) {
            $values_5 = array();
            foreach ($data->{'required'} as $value_6) {
                $values_5[] = $value_6;
            }
            $object->setRequired($values_5);
        }
        if (isset($data->{'additionalProperties'})) {
            $value_7 = null;
            if (is_bool($data->{'additionalProperties'})) {
                $value_7 = $data->{'additionalProperties'};
            }
            if (is_object($data->{'additionalProperties'})) {
                $value_7 = $this->normalizerChain->denormalize($data->{'additionalProperties'}, 'Joli\\Jane\\Model\\JsonSchema', 'json', $context);
            }
            $object->setAdditionalProperties($value_7);
        }
        if (isset($data->{'definitions'})) {
            $values_8 = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'definitions'} as $key => $value_9) {
                $values_8[$key] = $this->normalizerChain->denormalize($value_9, 'Joli\\Jane\\Model\\JsonSchema', 'json', $context);
            }
            $object->setDefinitions($values_8);
        }
        if (isset($data->{'properties'})) {
            $values_10 = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'properties'} as $key_11 => $value_12) {
                $values_10[$key_11] = $this->normalizerChain->denormalize($value_12, 'Joli\\Jane\\Model\\JsonSchema', 'json', $context);
            }
            $object->setProperties($values_10);
        }
        if (isset($data->{'patternProperties'})) {
            $values_13 = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'patternProperties'} as $key_14 => $value_15) {
                $values_13[$key_14] = $this->normalizerChain->denormalize($value_15, 'Joli\\Jane\\Model\\JsonSchema', 'json', $context);
            }
            $object->setPatternProperties($values_13);
        }
        if (isset($data->{'dependencies'})) {
            $values_16 = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'dependencies'} as $key_17 => $value_18) {
                $value_19 = null;
                if (is_object($value_18)) {
                    $value_19 = $this->normalizerChain->denormalize($value_18, 'Joli\\Jane\\Model\\JsonSchema', 'json', $context);
                }
                if (is_array($value_18)) {
                    $values_20 = array();
                    foreach ($value_18 as $value_21) {
                        $values_20[] = $value_21;
                    }
                    $value_19 = $values_20;
                }
                $values_16[$key_17] = $value_19;
            }
            $object->setDependencies($values_16);
        }
        if (isset($data->{'enum'})) {
            $object->setEnum($data->{'enum'});
        }
        if (isset($data->{'type'})) {
            $value_22 = null;
            if (isset($data->{'type'})) {
                $value_22 = $data->{'type'};
            }
            if (is_array($data->{'type'})) {
                $values_23 = array();
                foreach ($data->{'type'} as $value_24) {
                    $values_23[] = $value_24;
                }
                $value_22 = $values_23;
            }
            $object->setType($value_22);
        }
        if (isset($data->{'format'})) {
            $object->setFormat($data->{'format'});
        }
        if (isset($data->{'allOf'})) {
            $values_25 = array();
            foreach ($data->{'allOf'} as $value_26) {
                $values_25[] = $this->normalizerChain->denormalize($value_26, 'Joli\\Jane\\Model\\JsonSchema', 'json', $context);
            }
            $object->setAllOf($values_25);
        }
        if (isset($data->{'anyOf'})) {
            $values_27 = array();
            foreach ($data->{'anyOf'} as $value_28) {
                $values_27[] = $this->normalizerChain->denormalize($value_28, 'Joli\\Jane\\Model\\JsonSchema', 'json', $context);
            }
            $object->setAnyOf($values_27);
        }
        if (isset($data->{'oneOf'})) {
            $values_29 = array();
            foreach ($data->{'oneOf'} as $value_30) {
                $values_29[] = $this->normalizerChain->denormalize($value_30, 'Joli\\Jane\\Model\\JsonSchema', 'json', $context);
            }
            $object->setOneOf($values_29);
        }
        if (isset($data->{'not'})) {
            $object->setNot($this->normalizerChain->denormalize($data->{'not'}, 'Joli\\Jane\\Model\\JsonSchema', 'json', $context));
        }

        return $object;
    }
}
