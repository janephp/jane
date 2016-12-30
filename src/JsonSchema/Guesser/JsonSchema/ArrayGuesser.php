<?php

namespace Joli\Jane\JsonSchema\Guesser\JsonSchema;

use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\JsonSchema\Guesser\GuesserInterface;
use Joli\Jane\JsonSchema\Guesser\TypeGuesserInterface;

use Joli\Jane\JsonSchema\Registry\Registry;
use Joli\Jane\Model\JsonSchema;
use Symfony\Component\PropertyInfo\Type;

class ArrayGuesser implements GuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface
{
    use ChainGuesserAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function supportObject($object)
    {
        return (($object instanceof JsonSchema) && $object->getType() === 'array');
    }

    /**
     * {@inheritDoc}
     */
    public function guessTypes($object, $name, Registry $registry)
    {
        $items = $object->getItems();

        if ($items === null) {
            return [];
        }

        if (!is_array($items)) {
            $types = [];

            /** @var Type $type */
            foreach ($this->chainGuesser->guessTypes($items, $name, $registry) as $type) {
                $types[] = new Type(Type::BUILTIN_TYPE_ARRAY, true, null, true, null, $type);
            }

            return $types;
        }

        $types = [];

        /** @var Type $type */
        foreach ($items as $item) {
            foreach ($this->chainGuesser->guessTypes($item, $name, $registry) as $type) {
                $types[] = new Type(Type::BUILTIN_TYPE_ARRAY, true, null, true, null, $type);
            }
        }

        return $types;
    }
}
