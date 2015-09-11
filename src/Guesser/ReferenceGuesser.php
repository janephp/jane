<?php

namespace Joli\Jane\Guesser;

use Joli\Jane\Guesser\Guess\ObjectType;
use Joli\Jane\Reference\Reference;
use Joli\Jane\Reference\Resolver;

class ReferenceGuesser implements GuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface
{
    use ChainGuesserAwareTrait;

    /**
     * @var Resolver
     */
    private $resolver;

    public function __construct(Resolver $resolver)
    {
        $this->resolver     = $resolver;
    }

    /**
     * {@inheritDoc}
     */
    public function supportObject($object)
    {
        return $object instanceof Reference;
    }

    /**
     * {@inheritDoc}
     */
    public function guessType($object, $name, $classes)
    {
        $resolved = $this->resolver->resolve($object);

        if (array_key_exists(spl_object_hash($resolved), $classes)) {
            $name = $classes[spl_object_hash($resolved)]->getName();
        }

        return $this->chainGuesser->guessType($resolved, $name, $classes);
    }
} 
