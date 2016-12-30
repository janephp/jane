<?php

namespace Joli\Jane\JsonSchema\Guesser\JsonSchema;

use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\JsonSchema\Guesser\GuesserInterface;
use Joli\Jane\JsonSchema\Guesser\TypeGuesserInterface;
use Joli\Jane\JsonSchema\Model\JsonSchema;
use Joli\Jane\JsonSchema\Registry\Registry;
use Symfony\Component\PropertyInfo\Type;

class AdditionalPropertiesGuesser implements GuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface
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

        if ($object->getAdditionalProperties() !== true && !is_object($object->getAdditionalProperties())) {
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
        if ($object->getAdditionalProperties() === true) {
            return [new Type(Type::BUILTIN_TYPE_OBJECT, true, \ArrayObject::class, true, new Type(Type::BUILTIN_TYPE_STRING))];
        }

        $types = [];

        foreach ($this->chainGuesser->guessTypes($object->getAdditionalProperties(), $name, $registry) as $type) {
            $types[] = new Type(Type::BUILTIN_TYPE_OBJECT, true, \ArrayObject::class, true, new Type(Type::BUILTIN_TYPE_STRING), $type);
        }

        return $types;
    }
}
