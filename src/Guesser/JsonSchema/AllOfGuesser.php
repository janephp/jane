<?php

namespace Joli\Jane\Guesser\JsonSchema;

use Joli\Jane\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\Guesser\Guess\Type;
use Joli\Jane\Guesser\GuesserInterface;
use Joli\Jane\Guesser\GuesserResolverTrait;
use Joli\Jane\Guesser\PropertiesGuesserInterface;
use Joli\Jane\Guesser\TypeGuesserInterface;
use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Reference\Resolver;
use Joli\Jane\Registry;
use Joli\Jane\Runtime\Reference;
use Joli\Jane\Schema;
use Symfony\Component\Serializer\SerializerInterface;

class AllOfGuesser implements GuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface, PropertiesGuesserInterface
{
    use ChainGuesserAwareTrait;
    use GuesserResolverTrait;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function guessType($object, $name, Registry $registry, Schema $schema)
    {
        $type = null;

        foreach ($object->getAllOf() as $allOf) {
            $allOfSchema = $allOf;

            if ($allOfSchema instanceof Reference) {
                $allOfSchema = $this->resolve($allOfSchema, JsonSchema::class);
            }

            if (null !== $allOfSchema->getType()) {
                if (null !== $type) {
                    throw new \RuntimeException('an allOf instruction with 2 or more types is strictly impossible, check your schema');
                }

                $type = $this->chainGuesser->guessType($allOf, $name, $registry, $schema);
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
    public function guessProperties($object, $name, Registry $registry)
    {
        $properties = [];
        foreach ($object->getAllOf() as $allOfSchema) {
            if ($allOfSchema instanceof Reference) {
                $allOfSchema = $this->resolve($allOfSchema, JsonSchema::class);
            }

            $properties = array_merge($properties, $this->chainGuesser->guessProperties($allOfSchema, $name, $registry));
        }

        return $properties;
    }
}
