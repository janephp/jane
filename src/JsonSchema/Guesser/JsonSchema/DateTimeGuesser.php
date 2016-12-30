<?php

namespace Joli\Jane\JsonSchema\Guesser\JsonSchema;

use Joli\Jane\JsonSchema\Guesser\GuesserInterface;
use Joli\Jane\JsonSchema\Guesser\TypeGuesserInterface;
use Joli\Jane\JsonSchema\Model\JsonSchema;
use Joli\Jane\JsonSchema\Registry\Registry;
use Symfony\Component\PropertyInfo\Type;

class DateTimeGuesser implements GuesserInterface, TypeGuesserInterface
{
    /** @var string Format of date to use */
    private $dateFormat;

    public function __construct($dateFormat = \DateTime::RFC3339)
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * {@inheritDoc}
     */
    public function supportObject($object)
    {
        return (($object instanceof JsonSchema) && $object->getType() === 'string' && $object->getFormat() === 'date-time');
    }

    /**
     * {@inheritDoc}
     *
     * @param JsonSchema $object
     */
    public function guessTypes($object, $name, Registry $registry)
    {
        return [new Type(Type::BUILTIN_TYPE_OBJECT, true, \DateTime::class)];
    }
}
