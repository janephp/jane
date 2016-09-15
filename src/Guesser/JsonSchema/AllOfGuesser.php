<?php

namespace Joli\Jane\Guesser\JsonSchema;

use Joli\Jane\Generator\Naming;
use Joli\Jane\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\Guesser\ClassGuesserInterface;
use Joli\Jane\Guesser\Guess\ClassGuess;
use Joli\Jane\Guesser\Guess\Type;
use Joli\Jane\Guesser\GuesserInterface;
use Joli\Jane\Guesser\PropertiesGuesserInterface;
use Joli\Jane\Guesser\TypeGuesserInterface;
use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Reference\Resolver;
use Joli\Jane\Runtime\Reference;

class AllOfGuesser implements GuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface, PropertiesGuesserInterface, ClassGuesserInterface
{
    use ChainGuesserAwareTrait;

    /**
     * @var \Joli\Jane\Generator\Naming
     */
    protected $naming;

    /**
     * @var \Joli\Jane\Reference\Resolver
     */
    protected $resolver;

    public function __construct(Naming $naming, Resolver $resolver)
    {
        $this->naming   = $naming;
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function guessType($object, $name, $classes)
    {
        $type = null;

        foreach ($object->getAllOf() as $allOf) {
            $allOfSchema = $allOf;

            if ($allOfSchema instanceof Reference) {
                $allOfSchema = $this->resolver->resolve($allOf);
            }

            if (null !== $allOfSchema->getType()) {
                if (null !== $type) {
                    throw new \RuntimeException(
                        'an allOf instruction with 2 or more types is strictly impossible, check your schema'
                    );
                }

                $type = $this->chainGuesser->guessType($allOf, $name, $classes);
            }
        }

        if ($type === null) {
            return new Type($object, 'mixed');
        }

        return $type;
    }

    /**
     * {@inheritdoc}
     */
    public function supportObject($object)
    {
        return ($object instanceof JsonSchema) && is_array($object->getAllOf()) && count($object->getAllOf()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function guessProperties($object, $name, $classes)
    {
        $properties = [];
        foreach ($object->getAllOf() as $allOfSchema) {
            if ($allOfSchema instanceof Reference) {
                $allOfSchema = $this->resolver->resolve($allOfSchema);
            }
            $properties = array_merge($properties, $this->chainGuesser->guessProperties($allOfSchema, $name, $classes));
        }

        return $properties;
    }

    /**
     * Guess model
     *
     * This guesser should create a Model and the associated File
     * The file must be inject into the context
     *
     * @param mixed  $object
     * @param string $name
     *
     * @return \Joli\Jane\Guesser\Guess\ClassGuess[] An array of class, key represent the hash of object mapped, and
     *                                               the value is the information about the class to be generated
     */
    public function guessClass($object, $name)
    {
        $types = [];

        foreach ($object->getAllOf() as $allOfSchema) {
            if ($allOfSchema instanceof Reference) {
                $definition = explode('/', $allOfSchema->getFragment());
                $definition = array_pop($definition);

                $resolvedReference = $this->resolver->resolve($allOfSchema);
                $includedClasses   = $this->chainGuesser->guessClass($resolvedReference, $definition);

                foreach ($includedClasses as $includedClass) {
                    $types[] = $includedClass->getName();
                }
            }
        }

        $classes = [
            spl_object_hash($object) =>
                new ClassGuess(
                    $object,
                    $this->naming->getClassName($name),
                    $types
                ),
        ];

        return $classes;
    }
}
