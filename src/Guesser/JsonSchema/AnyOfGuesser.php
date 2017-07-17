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

class AnyOfGuesser implements GuesserInterface, ClassGuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface
{
    use ChainGuesserAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function guessClass($object, $name, $reference, Registry $registry)
    {
        foreach ($object->getAnyOf() as $anyOfKey => $anyOfObject) {
            $this->chainGuesser->guessClass($anyOfObject, $name.'AnyOf', $reference . '/anyOf/' . $anyOfKey, $registry);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function guessType($object, $name, $reference, Registry $registry)
    {
        if (count($object->getAnyOf()) == 1) {
            return $this->chainGuesser->guessType($object->getAnyOf()[0], $name, $reference . '/anyOf/0', $registry);
        }

        $type = new MultipleType($object);

        foreach ($object->getAnyOf() as $anyOfKey => $anyOfObject) {
            $type->addType($this->chainGuesser->guessType($anyOfObject, $name, $reference . '/anyOf/' . $anyOfKey, $registry));
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
