<?php

namespace Joli\Jane\Guesser\JsonSchema;

use Joli\Jane\Guesser\Guess\DateTimeType;
use Joli\Jane\Guesser\GuesserInterface;
use Joli\Jane\Guesser\TypeGuesserInterface;
use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Registry;
use Joli\Jane\Schema;

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
        return (($object instanceof JsonSchema) && $object->getType() === 'string' && in_array($object->getFormat(), ['date', 'date-time']));
    }

    /**
     * {@inheritDoc}
     */
    public function guessType($object, $name, Registry $registry, Schema $schema)
    {
        return new DateTimeType($object, $object->getFormat() === 'date' ? 'Y-m-d' : $this->dateFormat);
    }
}
