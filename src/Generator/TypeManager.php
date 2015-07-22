<?php

namespace Joli\Jane\Generator;

use Joli\Jane\Reference\Reference;
use Joli\Jane\Reference\Resolver;
use Joli\Jane\Schema\Schema;

class TypeManager
{
    private $referencedType = [];
    private $resolver;

    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Get type of an object in the @var format
     *
     * @param Schema           $rootSchema
     * @param Schema|Reference $object
     * @param string           $key
     * @param string           $namespace
     *
     * @return string
     */
    public function getVariableTagString(Schema $rootSchema, $object, $key, $namespace)
    {
        $types = $this->getTypes($rootSchema, $object);

        if (($objectKey = array_search('object', $types)) !== false) {
            $types[$objectKey] = $namespace . "\\" . ucfirst($key);
        }

        return implode('|', $types);
    }

    /**
     * Get all types for a schema
     *
     * @param Schema           $rootSchema
     * @param Schema|Reference $object
     *
     * @return array
     */
    public function getTypes(Schema $rootSchema, $object)
    {
        $schema = $object;

        if ($object instanceof Reference) {
            // Does this reference refer to a created object ?
            $schema = $this->resolver->resolve($object, $rootSchema);
        }

        $types = $this->resolveTypes($rootSchema, $schema);

        return $types;
    }

    protected function resolveTypes(Schema $rootSchema, Schema $schema)
    {
        if ($schema->getType() !== null) {
            return is_array($schema->getType()) ? $schema->getType() : [$schema->getType()];
        }

        if ($schema->getAnyOf() !== null) {
            $types = [];

            foreach ($schema->getAnyOf() as $subSchema) {
                $types = array_merge($types, $this->getTypes($rootSchema, $subSchema));
            }

            return $types;
        }

        if ($schema->getAllOf() !== null) {
            $types = [];

            foreach ($schema->getAllOf() as $subSchema) {
                $types += $this->getTypes($rootSchema, $subSchema);
            }

            return $types;
        }

        if ($schema->getOneOf() !== null) {
            $types = [];

            foreach ($schema->getOneOf() as $subSchema) {
                $types += $this->getTypes($rootSchema, $subSchema);
            }

            return $types;
        }

        return ['mixed'];
    }

    public function isObjectOfType(Schema $rootSchema, $object, $type)
    {
        return in_array($type, $this->getTypes($rootSchema, $object));
    }
}
 