<?php

namespace Joli\Jane\JsonSchema\Guesser\JsonSchema;

use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\JsonSchema\Guesser\GuesserInterface;
use Joli\Jane\JsonSchema\Guesser\TypeGuesserInterface;
use Joli\Jane\JsonSchema\Registry\Registry;
use Joli\Jane\Model\JsonSchema;

class PatternPropertiesGuesser implements GuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface
{
    use ChainGuesserAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function supportObject($object)
    {
        if (!($object instanceof JsonSchema)) {
            return false;
        }

        if ($object->getType() !== 'object') {
            return false;
        }

        // @TODO Handle case when there is properties (need to rework the guessClass for extending \ArrayObject and do the assignation)
        if ($object->getProperties() !== null) {
            return false;
        }

        if (!($object->getPatternProperties() instanceof \ArrayObject) || count($object->getPatternProperties()) == 0) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @param JsonSchema $object
     */
    public function guessTypes($object, $name, Registry $registry)
    {
        $types = [];

        foreach ($object->getPatternProperties() as $pattern => $patternProperty) {
            $types = array_merge($types, $this->chainGuesser->guessTypes($patternProperty, $name, $registry));
        }

        return $types;
    }
}
