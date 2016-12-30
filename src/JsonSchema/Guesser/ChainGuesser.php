<?php

namespace Joli\Jane\JsonSchema\Guesser;

use Joli\Jane\JsonSchema\Registry\Registry;
use Symfony\Component\PropertyInfo\Type;

class ChainGuesser implements TypeGuesserInterface, PropertiesGuesserInterface, ModelGuesserInterface
{
    /**
     * @var GuesserInterface[]
     */
    private $guessers = [];

    public function addGuesser(GuesserInterface $guesser)
    {
        if ($guesser instanceof ChainGuesserAwareInterface) {
            $guesser->setChainGuesser($this);
        }

        $this->guessers[] = $guesser;
    }

    /**
     * {@inheritDoc}
     */
    public function registerModel($object, $name, $reference, Registry $registry)
    {
        foreach ($this->guessers as $guesser) {
            if (!($guesser instanceof ModelGuesserInterface)) {
                continue;
            }

            if ($guesser->supportObject($object)) {
                $guesser->registerModel($object, $name, $reference, $registry);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function guessTypes($object, $name, Registry $registry)
    {
        $types = [];

        foreach ($this->guessers as $guesser) {
            if (!($guesser instanceof TypeGuesserInterface)) {
                continue;
            }

            if ($guesser->supportObject($object)) {
                $types = array_merge($types, $guesser->guessTypes($object, $name, $registry));
            }
        }

        return $types;
    }

    /**
     * {@inheritDoc}
     */
    public function guessProperties($object, $name, Registry $registry)
    {
        $properties = [];

        foreach ($this->guessers as $guesser) {
            if (!($guesser instanceof PropertiesGuesserInterface)) {
                continue;
            }

            if ($guesser->supportObject($object)) {
                $properties = array_merge($properties, $guesser->guessProperties($object, $name, $registry));
            }
        }

        return $properties;
    }
}
