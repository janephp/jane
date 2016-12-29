<?php

namespace Joli\Jane\Guesser;

use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\SerializerInterface;

class ReferenceGuesser implements ClassGuesserInterface, GuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface
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
    public function supportObject($object)
    {
        return $object instanceof Reference;
    }

    /**
     * {@inheritdoc}
     *
     * @param Reference $object
     */
    public function guessClass($object, $name, $reference)
    {
        if ($object->isInCurrentDocument()) {
            return [];
        }

        return $this->chainGuesser->guessClass(
            $this->resolve($object, JsonSchema::class),
            $name,
            (string) $object->getMergedUri()
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param Reference $object
     */
    public function guessType($object, $name, $classes)
    {
        $resolved = $this->resolve($object, JsonSchema::class);
        $classKey = (string) $object->getMergedUri();

        if ((string)$object->getMergedUri() === (string)$object->getMergedUri()->withFragment('')) {
            $classKey .= '#';
        }

        if (array_key_exists($classKey, $classes)) {
            $name = $classes[$classKey]->getName();
        }

        return $this->chainGuesser->guessType($resolved, $name, $classes);
    }
}
