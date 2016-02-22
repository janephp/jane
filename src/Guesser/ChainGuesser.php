<?php

namespace Joli\Jane\Guesser;

use Joli\Jane\Guesser\Guess\MultipleType;
use Joli\Jane\Guesser\Guess\Type;
use Joli\Jane\Guesser\JsonSchema\DateTimeGuesser;

class ChainGuesser implements TypeGuesserInterface, PropertiesGuesserInterface, ClassGuesserInterface
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
    public function guessClass($object, $name)
    {
        $classes = [];

        foreach ($this->guessers as $guesser) {
            if (!($guesser instanceof ClassGuesserInterface)) {
                continue;
            }

            if ($guesser->supportObject($object)) {
                $classes = array_merge($classes, $guesser->guessClass($object, $name));
            }
        }

        return $classes;
    }

    /**
     * {@inheritDoc}
     */
    public function guessType($object, $name, $classes)
    {
        $type = null;

        foreach ($this->guessers as $guesser) {
            if (!($guesser instanceof TypeGuesserInterface)) {
                continue;
            }

            if ($guesser->supportObject($object)) {
                if ($type === null) {
                    $type = $guesser->guessType($object, $name, $classes);

                    // DateTime guesser should not end up in multiple types
                    // TODO: there should be a more generic solution for this
                    if ($guesser instanceof DateTimeGuesser) {
                        break;
                    } else {
                        continue;
                    }
                }

                if (!$type instanceof MultipleType) {
                    $type = new MultipleType($object, [$type]);
                }

                $type->addType($guesser->guessType($object, $name, $classes));
            }
        }

        if ($type === null) {
            return new Type($object, 'mixed');
        }

        return $type;
    }

    /**
     * {@inheritDoc}
     */
    public function guessProperties($object, $name, $classes)
    {
        $properties = [];

        foreach ($this->guessers as $guesser) {
            if (!($guesser instanceof PropertiesGuesserInterface)) {
                continue;
            }

            if ($guesser->supportObject($object)) {
                $properties = array_merge($properties, $guesser->guessProperties($object, $name, $classes));
            }
        }

        return $properties;
    }
}
