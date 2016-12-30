<?php

namespace Joli\Jane\JsonSchema\Guesser\JsonSchema;

use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\JsonSchema\Guesser\ModelGuesserInterface;
use Joli\Jane\JsonSchema\Guesser\GuesserInterface;
use Joli\Jane\JsonSchema\Model\JsonSchema;
use Joli\Jane\JsonSchema\Registry\Registry;

class ItemsGuesser implements GuesserInterface, ModelGuesserInterface, ChainGuesserAwareInterface
{
    use ChainGuesserAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param JsonSchema $object
     */
    public function registerModel($object, $name, $reference, Registry $registry)
    {
        if ($object->getItems() instanceof JsonSchema) {
            $this->chainGuesser->registerModel($object->getAdditionalItems(), $name . 'Item', $reference . '/additionalItems', $registry);

            return;
        }

        foreach ($object->getItems() as $key => $item) {
            $this->chainGuesser->registerModel($item, $name . 'Item' . $key, $reference . '/additionalItems/' . $key, $registry);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supportObject($object)
    {
        return (
            ($object instanceof JsonSchema)
            && (
                $object->getItems() instanceof JsonSchema
                ||
                (
                    is_array($object->getItems())
                    &&
                    count($object->getItems()) > 0
                )
            )
        );
    }
}
