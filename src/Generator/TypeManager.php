<?php

namespace Joli\Jane\Generator;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Reference\Reference;
use Joli\Jane\Reference\Resolver;
use Joli\Jane\Schema\Schema;

class TypeManager
{
    private $resolver;

    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Get type of an object in the @var format
     *
     * @param Schema|Reference $object
     * @param string           $key
     * @param Context          $context
     *
     * @return string
     */
    public function getVariableTagString($object, $key, Context $context)
    {
        $types = $this->getTypes($object, $context);

        if (($objectKey = array_search('object', $types)) !== false) {
            $types[$objectKey] = "\\" . $context->getNamespace() . "\\" . ucfirst($key);
        }

        return implode('|', $types);
    }

    /**
     * Get all types for a schema
     *
     * @param Schema|Reference $object
     * @param Context          $context
     *
     * @return array
     */
    public function getTypes($object, Context $context)
    {
        $schema = $object;

        if ($object instanceof Reference) {
            // Does this reference refer to a created object ?
            $schema = $this->resolver->resolve($object, $context->getRootSchema());

            if ($context->getSchemaObjectMap()->hasSchema($schema)) {
                return ["\\" . $context->getSchemaObjectMap()->getObject($schema)->getFullyQualifiedName()];
            }
        }

        $types = $this->resolveTypes($schema, $context);

        return $types;
    }

    protected function resolveTypes(Schema $schema, Context $context)
    {
        if ($schema->getType() !== null) {
            return is_array($schema->getType()) ? $schema->getType() : [$schema->getType()];
        }

        if ($schema->getAnyOf() !== null) {
            $types = [];

            foreach ($schema->getAnyOf() as $subSchema) {
                $types = array_merge($types, $this->getTypes($subSchema, $context));
            }

            return $types;
        }

        if ($schema->getAllOf() !== null) {
            $types = [];

            foreach ($schema->getAllOf() as $subSchema) {
                $types += $this->getTypes($subSchema, $context);
            }

            return $types;
        }

        if ($schema->getOneOf() !== null) {
            $types = [];

            foreach ($schema->getOneOf() as $subSchema) {
                $types += $this->getTypes($subSchema, $context);
            }

            return $types;
        }

        return ['mixed'];
    }

    /**
     * Whether an object is of type wanted
     *
     * @param Schema|Reference $object
     * @param string           $type
     * @param Context          $context
     *
     * @return bool
     */
    public function isObjectOfType($object, $type, Context $context)
    {
        return in_array($type, $this->getTypes($object, $context));
    }
}
 