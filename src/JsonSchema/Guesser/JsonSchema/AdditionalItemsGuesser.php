<?php

namespace Joli\Jane\JsonSchema\Guesser\JsonSchema;

use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\JsonSchema\Guesser\GuesserInterface;
use Joli\Jane\JsonSchema\Guesser\ModelGuesserInterface;
use Joli\Jane\JsonSchema\Model\JsonSchema;
use Joli\Jane\JsonSchema\Registry\Registry;

class AdditionalItemsGuesser implements ChainGuesserAwareInterface, GuesserInterface, ModelGuesserInterface
{
    use ChainGuesserAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param JsonSchema
     */
    public function registerModel($object, $name, $reference, Registry $registry)
    {
        return $this->chainGuesser->registerModel($object->getAdditionalItems(), $name . 'AdditionalItems', $reference . '/additionalItems', $registry);
    }

    /**
     * {@inheritDoc}
     */
    public function supportObject($object)
    {
        return ($object instanceof JsonSchema) && ($object->getAdditionalItems() instanceof JsonSchema);
    }
}
