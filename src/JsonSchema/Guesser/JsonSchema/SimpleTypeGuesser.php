<?php

namespace Joli\Jane\JsonSchema\Guesser\JsonSchema;

use Joli\Jane\JsonSchema\Guesser\GuesserInterface;
use Joli\Jane\JsonSchema\Guesser\TypeGuesserInterface;
use Joli\Jane\JsonSchema\Model\JsonSchema;
use Joli\Jane\JsonSchema\Registry\Registry;
use Symfony\Component\PropertyInfo\Type;

class SimpleTypeGuesser implements GuesserInterface, TypeGuesserInterface
{
    protected $typesSupported = [
        'boolean',
        'integer',
        'number',
        'string',
        'null',
    ];

    protected $phpTypesMapping = [
        'boolean' => Type::BUILTIN_TYPE_BOOL,
        'integer' => Type::BUILTIN_TYPE_INT,
        'number' => Type::BUILTIN_TYPE_FLOAT,
        'string' => Type::BUILTIN_TYPE_STRING,
        'null' => Type::BUILTIN_TYPE_NULL,
    ];

    protected $excludeFormat = [
        'string' => [
            'date-time',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function supportObject($object)
    {
        return ($object instanceof JsonSchema)
            &&
            in_array($object->getType(), $this->typesSupported)
            &&
            (
                !in_array($object->getType(), $this->excludeFormat)
                ||
                !in_array($object->getFormat(), $this->excludeFormat[$object->getType()])
            )
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @param JsonSchema $object
     */
    public function guessTypes($object, $name, Registry $registry)
    {
        return [new Type($this->phpTypesMapping[$object->getType()], true)];
    }
}
