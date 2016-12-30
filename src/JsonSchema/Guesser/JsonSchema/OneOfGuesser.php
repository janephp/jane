<?php

namespace Joli\Jane\JsonSchema\Guesser\JsonSchema;

use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\JsonSchema\Guesser\GuesserInterface;
use Joli\Jane\JsonSchema\Guesser\TypeGuesserInterface;
use Joli\Jane\JsonSchema\Model\JsonSchema;
use Joli\Jane\JsonSchema\Registry\Registry;

class OneOfGuesser implements ChainGuesserAwareInterface, TypeGuesserInterface, GuesserInterface
{
    use ChainGuesserAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function supportObject($object)
    {
        return (($object instanceof JsonSchema) && $object->getType() !== "object" && is_array($object->getOneOf()) && count($object->getOneOf()) > 0);
    }

    /**
     * {@inheritDoc}
     */
    public function guessTypes($object, $name, Registry $registry)
    {
        $types = [];

        foreach ($object->getOneOf() as $oneOf) {
            $types = array_merge($types, $this->chainGuesser->guessTypes($oneOf, $name, $registry));
        }

        return $types;
    }
}
