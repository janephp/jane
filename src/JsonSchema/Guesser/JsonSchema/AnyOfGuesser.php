<?php

namespace Joli\Jane\JsonSchema\Guesser\JsonSchema;

use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\JsonSchema\Guesser\ModelGuesserInterface;
use Joli\Jane\JsonSchema\Guesser\GuesserInterface;
use Joli\Jane\JsonSchema\Guesser\TypeGuesserInterface;

use Joli\Jane\JsonSchema\Registry\Registry;
use Joli\Jane\Model\JsonSchema;

class AnyOfGuesser implements GuesserInterface, ModelGuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface
{
    use ChainGuesserAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function registerModel($object, $name, $reference, Registry $registry)
    {
        $classes = [];

        foreach ($object->getAnyOf() as $anyOfObject) {
            $classes = array_merge($classes, $this->chainGuesser->guessClass($anyOfObject, $name.'AnyOf', $reference . '/anyOf'));
        }

        return $classes;
    }

    /**
     * {@inheritDoc}
     *
     * @param JsonSchema $object
     */
    public function guessTypes($object, $name, Registry $registry)
    {
        if (count($object->getAnyOf()) === 1) {
            return $this->chainGuesser->guessTypes($object->getAnyOf()[0], $name, $registry);
        }

        $types = [];

        foreach ($object->getAnyOf() as $anyOfObject) {
            $types = array_merge($types, $this->chainGuesser->guessTypes($anyOfObject, $name, $registry));
        }

        return $types;
    }

    /**
     * {@inheritDoc}
     */
    public function supportObject($object)
    {
        return (($object instanceof JsonSchema) && is_array($object->getAnyOf()) && count($object->getAnyOf()) > 0);
    }
}
