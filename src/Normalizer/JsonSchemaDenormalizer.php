<?php

namespace Joli\Jane\Normalizer;

use Joli\Jane\Reference\Reference;
use Joli\Jane\Model\JsonSchema;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class JsonSchemaDenormalizer implements DenormalizerInterface
{
    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritDoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $schema   = $this->createSchema($data);

        return $schema;
    }

    /**
     * Create a Schema given a set of data
     *
     * @param $data
     *
     * @return Reference|JsonSchema
     */
    protected function createSchema($data)
    {
        // Case of empty schema (maybe use a specific class ?)
        if (empty($data)) {
            return null;
        }

        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'});
        }

        $schema      = new JsonSchema();

        $this->setFields([
            'id', 'title', 'description', 'default', 'multipleOf', 'maximum', 'exclusiveMaximum',
            'minimum', 'exclusiveMinimum', 'maxLength', 'minLength', 'pattern', 'maxItems', 'minItems',
            'uniqueItems', 'maxProperties', 'minProperties', 'required', 'enum', 'type', 'format'
        ], $data, $schema);

        if (isset($data->{'$schema'})) {
            $schema->setDollarSchema($data->{'$schema'});
        }

        if (isset($data->additionalItems)) {
            if (is_object($data->additionalItems)) {
                $schema->setAdditionalItems($this->createSchema($data->additionalItems));
            }

            if (is_bool($data->additionalItems)) {
                $schema->setAdditionalItems($data->additionalItems);
            }
        }

        if (isset($data->items)) {
            if (is_object($data->items)) {
                $schema->setItems($this->createSchema($data->items));
            }

            if (is_array($data->items)) {
                $schemaArray = new \ArrayObject([]);

                foreach ($data->items as $item) {
                    $schemaArray[] = $this->createSchema($item);
                }

                $schema->setItems($schemaArray);
            }
        }

        if (isset($data->additionalProperties)) {
            if (is_object($data->additionalProperties)) {
                $schema->setAdditionalProperties($this->createSchema($data->additionalProperties));
            }

            if (is_bool($data->additionalProperties)) {
                $schema->setAdditionalProperties($data->additionalProperties);
            }
        }

        $this->hydrateSchemaArrayAssoc('definitions', $data, $schema);
        $this->hydrateSchemaArrayAssoc('properties', $data, $schema);
        $this->hydrateSchemaArrayAssoc('patternProperties', $data, $schema);

        if (isset($data->dependencies)) {
            $schemaArray = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);

            foreach ($data->dependencies as $key => $dependency) {
                if (is_object($dependency)) {
                    $schemaArray[$key] = $this->createSchema($dependency);
                }

                if (is_array($dependency)) {
                    $schemaArray[$key] = $dependency;
                }
            }

            $schema->setDependencies($schemaArray);
        }

        $this->hydrateSchemaArrayAssoc('allOf', $data, $schema);
        $this->hydrateSchemaArrayAssoc('anyOf', $data, $schema);
        $this->hydrateSchemaArrayAssoc('oneOf', $data, $schema);

        if (isset($data->not)) {
            $schema->setNot($this->createSchema($data->not));
        }

        return $schema;
    }

    /**
     * Set a value as an array of schema without keys
     *
     * @param $field
     * @param $data
     * @param $schema
     * @param null $default
     */
    protected function hydrateSchemaArray($field, $data, $schema, $default = null)
    {
        if (!$this->propertyAccessor->isWritable($schema, $field)) {
            return;
        }

        $this->propertyAccessor->setValue($schema, $field, $default);

        if (isset($data->{$field})) {
            $schemaArray = new \ArrayObject([]);

            foreach ($data->{$field} as $value) {
                $schemaArray[] = $this->createSchema($value);
            }

            $this->propertyAccessor->setValue($schema, $field, $schemaArray);
        }
    }

    /**
     * Set a value as an array of schema with keys
     *
     * @param $field
     * @param $data
     * @param $schema
     * @param null $default
     */
    protected function hydrateSchemaArrayAssoc($field, $data, $schema, $default = null)
    {
        if (!$this->propertyAccessor->isWritable($schema, $field)) {
            return;
        }

        $this->propertyAccessor->setValue($schema, $field, $default);

        if (isset($data->{$field})) {
            $schemaArray = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);

            foreach ($data->{$field} as $key => $value) {
                $schemaArray[$key] = $this->createSchema($value);
            }

            $this->propertyAccessor->setValue($schema, $field, $schemaArray);
        }
    }

    protected function setFields($fields, $data, $schema)
    {
        foreach ($fields as $field) {
            if (isset($data->{$field}) && $this->propertyAccessor->isWritable($schema, $field)) {
                $this->propertyAccessor->setValue($schema, $field, $data->{$field});
            }
        }
    }

    /**
     * Checks whether the given class is supported for denormalization by this normalizer.
     *
     * @param mixed $data Data to denormalize from.
     * @param string $type The class to which the data should be denormalized.
     * @param string $format The format being deserialized from.
     *
     * @return bool
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== JsonSchema::class) {
            return false;
        }

        if ($format !== 'json') {
            return false;
        }

        return true;
    }
}
 