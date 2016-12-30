<?php

namespace Joli\Jane\JsonSchema\Guesser\JsonSchema;

use Joli\Jane\JsonReference\Reference;
use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\JsonSchema\Guesser\GuesserInterface;
use Joli\Jane\JsonSchema\Guesser\GuesserResolverTrait;
use Joli\Jane\JsonSchema\Guesser\PropertiesGuesserInterface;
use Joli\Jane\JsonSchema\Guesser\TypeGuesserInterface;
use Joli\Jane\JsonSchema\Model\JsonSchema;
use Joli\Jane\JsonSchema\Registry\Registry;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AllOfGuesser implements GuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface, PropertiesGuesserInterface
{
    use ChainGuesserAwareTrait;
    use GuesserResolverTrait;

    public function __construct(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function guessTypes($object, $name, Registry $registry)
    {
        $types = null;

        foreach ($object->getAllOf() as $allOf) {
            $allOfSchema = $allOf;

            if ($allOfSchema instanceof Reference) {
                $allOfSchema = $this->resolve($allOfSchema, JsonSchema::class);
            }

            if (null !== $allOfSchema->getType()) {
                if (null !== $types) {
                    throw new \RuntimeException('an allOf instruction with 2 or more types is strictly impossible, check your schema');
                }

                $types = $this->chainGuesser->guessTypes($allOf, $name, $registry);
            }
        }

        if ($types === null) {
            return [];
        }

        return $types;
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
