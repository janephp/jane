<?php
namespace Joli\Jane\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Joli\Jane\Reference\Reference;

class JsonSchemaNormalizer implements DenormalizerInterface
{
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
            if (is_int($data->{'minLength'})) {
                $object->setMinLength($data->{'minLength'});
            }

            if (isset($data->{'minLength'})) {
                $object->setMinLength($data->{'minLength'});
            }

        }

        if (isset($data->{'pattern'})) {
            $object->setPattern($data->{'pattern'});
        }

        if (isset($data->{'additionalItems'})) {
            if (is_bool($data->{'additionalItems'})) {
                $object->setAdditionalItems($data->{'additionalItems'});
            }

            if (is_object($data->{'additionalItems'})) {
                $object->setAdditionalItems($this->denormalize($data->{'additionalItems'}, '\Joli\Jane\Model\JsonSchema', 'json', $context));
            }

        }

        if (isset($data->{'items'})) {
            if (is_object($data->{'items'})) {
                $object->setItems($this->denormalize($data->{'items'}, '\Joli\Jane\Model\JsonSchema', 'json', $context));
            }

            if (is_array($data->{'items'})) {
                $values = [];

                foreach ($data->{'items'} as $value) {
                    $values[] = $this->denormalize($value, '\Joli\Jane\Model\JsonSchema', 'json', $context);
                }

                $object->setItems($values);

            }

        }

        if (isset($data->{'maxItems'})) {
            $object->setMaxItems($data->{'maxItems'});
        }

        if (isset($data->{'minItems'})) {
            if (is_int($data->{'minItems'})) {
                $object->setMinItems($data->{'minItems'});
            }

            if (isset($data->{'minItems'})) {
                $object->setMinItems($data->{'minItems'});
            }

        }

        if (isset($data->{'uniqueItems'})) {
            $object->setUniqueItems($data->{'uniqueItems'});
        }

        if (isset($data->{'maxProperties'})) {
            $object->setMaxProperties($data->{'maxProperties'});
        }

        if (isset($data->{'minProperties'})) {
            if (is_int($data->{'minProperties'})) {
                $object->setMinProperties($data->{'minProperties'});
            }

            if (isset($data->{'minProperties'})) {
                $object->setMinProperties($data->{'minProperties'});
            }

        }

        if (isset($data->{'required'})) {
            $values = [];

            foreach ($data->{'required'} as $value) {
                $values[] = $value;
            }

            $object->setRequired($values);

        }

        if (isset($data->{'additionalProperties'})) {
            if (is_bool($data->{'additionalProperties'})) {
                $object->setAdditionalProperties($data->{'additionalProperties'});
            }

            if (is_object($data->{'additionalProperties'})) {
                $object->setAdditionalProperties($this->denormalize($data->{'additionalProperties'}, '\Joli\Jane\Model\JsonSchema', 'json', $context));
            }

        }

        if (isset($data->{'definitions'})) {
            $values = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);

            foreach ($data->{'definitions'} as $key => $value) {
                $values[$key] = $this->denormalize($value, '\Joli\Jane\Model\JsonSchema', 'json', $context);
            }

            $object->setDefinitions($values);
        }

        if (isset($data->{'properties'})) {
            $values = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);

            foreach ($data->{'properties'} as $key => $value) {
                $values[$key] = $this->denormalize($value, '\Joli\Jane\Model\JsonSchema', 'json', $context);
            }

            $object->setProperties($values);
        }

        if (isset($data->{'patternProperties'})) {
            $values = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);

            foreach ($data->{'patternProperties'} as $key => $value) {
                $values[$key] = $this->denormalize($value, '\Joli\Jane\Model\JsonSchema', 'json', $context);
            }

            $object->setPatternProperties($values);
        }

        if (isset($data->{'dependencies'})) {
            $values = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);

            foreach ($data->{'dependencies'} as $key => $value) {
                if (is_object($value)) {
                    $values[$key] = $this->denormalize($value, '\Joli\Jane\Model\JsonSchema', 'json', $context);
                }

                if (is_array($value)) {
                    $values = [];

                    foreach ($data->{'dependencies'} as $value) {
                        $values[] = $value;
                    }

                    $object->setDependencies($values);

                }

            }

            $object->setDependencies($values);
        }

        if (isset($data->{'enum'})) {
            $object->setEnum($data->{'enum'});
        }

        if (isset($data->{'type'})) {
            if (isset($data->{'type'})) {
                $object->setType($data->{'type'});
            }

            if (is_array($data->{'type'})) {
                $values = [];

                foreach ($data->{'type'} as $value) {
                    $values[] = $value;
                }

                $object->setType($values);

            }

        }

        if (isset($data->{'format'})) {
            $object->setFormat($data->{'format'});
        }

        if (isset($data->{'allOf'})) {
            $values = [];

            foreach ($data->{'allOf'} as $value) {
                $values[] = $this->denormalize($value, '\Joli\Jane\Model\JsonSchema', 'json', $context);
            }

            $object->setAllOf($values);

        }

        if (isset($data->{'anyOf'})) {
            $values = [];

            foreach ($data->{'anyOf'} as $value) {
                $values[] = $this->denormalize($value, '\Joli\Jane\Model\JsonSchema', 'json', $context);
            }

            $object->setAnyOf($values);

        }

        if (isset($data->{'oneOf'})) {
            $values = [];

            foreach ($data->{'oneOf'} as $value) {
                $values[] = $this->denormalize($value, '\Joli\Jane\Model\JsonSchema', 'json', $context);
            }

            $object->setOneOf($values);

        }

        if (isset($data->{'not'})) {
            $object->setNot($this->denormalize($data->{'not'}, '\Joli\Jane\Model\JsonSchema', 'json', $context));
        }

        return $object;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Joli\Jane\Model\JsonSchema') {
            return false;
        }

        if ($format !== 'json') {
            return false;
        }

        return true;
    }
}
