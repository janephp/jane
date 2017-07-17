<?php

namespace Joli\Jane\Guesser\JsonSchema;

use Joli\Jane\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\Guesser\ClassGuesserInterface;
use Joli\Jane\Guesser\Guess\MapType;
use Joli\Jane\Guesser\Guess\Type;
use Joli\Jane\Guesser\GuesserInterface;
use Joli\Jane\Guesser\TypeGuesserInterface;
use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Registry;
use Joli\Jane\Schema;

class AdditionalPropertiesGuesser implements GuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface, ClassGuesserInterface
{
    use ChainGuesserAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function guessClass($object, $name, $reference, Registry $registry)
    {
        if (is_a($object->getAdditionalProperties(), $this->getSchemaClass())) {
            $this->chainGuesser->guessClass($object->getAdditionalProperties(), $name . 'Item', $reference . '/additionalProperties', $registry);
        }
    }

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

        if ($object->getAdditionalProperties() !== true && !is_object($object->getAdditionalProperties())) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function guessType($object, $name, Registry $registry, Schema $schema)
    {
        if ($object->getAdditionalProperties() === true) {
            return new MapType($object, new Type($object, 'mixed'));
        }

        return new MapType($object, $this->chainGuesser->guessType($object->getAdditionalProperties(), $name, $registry, $schema));
    }

    /**
     * @return string
     */
    protected function getSchemaClass()
    {
        return Schema::class;
    }
}
