<?php

namespace Joli\Jane\JsonSchema\Guesser\JsonSchema;

use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\JsonSchema\Guesser\ModelGuesserInterface;
use Joli\Jane\JsonSchema\Guesser\GuesserInterface;
use Joli\Jane\JsonSchema\Registry\Registry;
use Joli\Jane\Model\JsonSchema;

class DefinitionGuesser implements ChainGuesserAwareInterface, GuesserInterface, ModelGuesserInterface
{
    use ChainGuesserAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param JsonSchema $object
     */
    public function registerModel($object, $name, $reference, Registry $registry)
    {
        foreach ($object->getDefinitions() as $key => $definition) {
            $this->chainGuesser->registerModel($definition, $key, $reference . '/definitions/' . $key, $registry);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supportObject($object)
    {
        return ($object instanceof JsonSchema) && $object->getDefinitions() !== null && count($object->getDefinitions()) > 0;
    }
}
