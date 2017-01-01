<?php

namespace Joli\Jane\Guesser\JsonSchema;

use Joli\Jane\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\Guesser\ClassGuesserInterface;
use Joli\Jane\Guesser\Guess\MultipleType;
use Joli\Jane\Guesser\GuesserInterface;
use Joli\Jane\Guesser\TypeGuesserInterface;

use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Registry;
use Joli\Jane\Schema;

class AnyOfGuesser implements GuesserInterface, ClassGuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface
{
    use ChainGuesserAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function guessClass($object, $name, $reference, Registry $registry)
    {
        foreach ($object->getAnyOf() as $anyOfObject) {
            $this->chainGuesser->guessClass($anyOfObject, $name.'AnyOf', $reference . '/anyOf', $registry);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function guessType($object, $name, Registry $registry, Schema $schema)
    {
        if (count($object->getAnyOf()) == 1) {
            return $this->chainGuesser->guessType($object->getAnyOf()[0], $name, $registry, $schema);
        }

        $type = new MultipleType($object);

        foreach ($object->getAnyOf() as $anyOfObject) {
            $type->addType($this->chainGuesser->guessType($anyOfObject, $name, $registry, $schema));
        }

        return $type;
    }

    /**
     * {@inheritDoc}
     */
    public function supportObject($object)
    {
        return (($object instanceof JsonSchema) && is_array($object->getAnyOf()) && count($object->getAnyOf()) > 0);
    }
}
