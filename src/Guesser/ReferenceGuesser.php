<?php

namespace Joli\Jane\Guesser;

use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\SerializerInterface;

class ReferenceGuesser implements GuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface
{
    use ChainGuesserAwareTrait;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function supportObject($object)
    {
        return $object instanceof Reference;
    }

    /**
     * {@inheritdoc}
     *
     * @param Reference $object
     */
    public function guessType($object, $name, $classes)
    {
        $resolved = $object->resolve(function ($data) use($object, $name) {
            return $this->serializer->denormalize($data, JsonSchema::class, 'json', [
                'schema-origin' => $object->getUri()
            ]);
        });

        if (array_key_exists($object->getUri(true), $classes)) {
            $name = $classes[$object->getUri(true)]->getName();
        }

        return $this->chainGuesser->guessType($resolved, $name, $classes);
    }
}
