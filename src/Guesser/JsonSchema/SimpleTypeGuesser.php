<?php

namespace Joli\Jane\Guesser\JsonSchema;

use Joli\Jane\Generator\Context\Context;

use Joli\Jane\Guesser\Guess\Property;
use Joli\Jane\Guesser\Guess\Type;
use Joli\Jane\Guesser\GuesserInterface;
use Joli\Jane\Guesser\PropertiesGuesserInterface;

use Joli\Jane\Guesser\TypeGuesserInterface;
use Joli\Jane\Model\JsonSchema;

class SimpleTypeGuesser implements GuesserInterface, TypeGuesserInterface
{
    protected $typesSupported = [
        'boolean',
        'integer',
        'number',
        'string',
        'null'
    ];

    protected $phpTypesMapping = [
        'boolean' => 'bool',
        'integer' => 'int',
        'number'  => 'float',
        'string'  => 'string',
        'null'    => 'null'
    ];

    /**
     * {@inheritDoc}
     */
    public function supportObject($object)
    {
        return ($object instanceof JsonSchema) && in_array($object->getType(), $this->typesSupported);
    }

    /**
     * {@inheritDoc}
     */
    public function guessType($object, $name, $classes)
    {
        return new Type($object, $this->phpTypesMapping[$object->getType()]);
    }
} 
