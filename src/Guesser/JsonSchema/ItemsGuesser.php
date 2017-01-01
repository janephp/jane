<?php

namespace Joli\Jane\Guesser\JsonSchema;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\Guesser\ClassGuesserInterface;
use Joli\Jane\Guesser\GuesserInterface;
use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Registry;

class ItemsGuesser implements GuesserInterface, ClassGuesserInterface, ChainGuesserAwareInterface
{
    use ChainGuesserAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function guessClass($object, $name, $reference, Registry $registry)
    {
        if ($object->getItems() instanceof JsonSchema) {
            $this->chainGuesser->guessClass($object->getAdditionalItems(), $name . 'Item', $reference . '/additionalItems', $registry);
        } else {
            foreach ($object->getItems() as $key => $item) {
                $this->chainGuesser->guessClass($item, $name . 'Item' . $key, $reference . '/additionalItems/' . $key, $registry);
            }
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
