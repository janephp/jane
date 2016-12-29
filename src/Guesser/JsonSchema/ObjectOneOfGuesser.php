<?php

namespace Joli\Jane\Guesser\JsonSchema;

use Joli\Jane\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\Guesser\ClassGuesserInterface;
use Joli\Jane\Guesser\Guess\MultipleType;
use Joli\Jane\Guesser\GuesserInterface;
use Joli\Jane\Guesser\GuesserResolverTrait;
use Joli\Jane\Guesser\TypeGuesserInterface;
use Joli\Jane\JsonSchemaMerger;
use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\SerializerInterface;

class ObjectOneOfGuesser implements GuesserInterface, TypeGuesserInterface, ClassGuesserInterface, ChainGuesserAwareInterface
{
    use ChainGuesserAwareTrait;
    use GuesserResolverTrait;

    /**
     * @var \Joli\Jane\JsonSchemaMerger
     */
    private $jsonSchemaMerger;

    public function __construct(JsonSchemaMerger $jsonSchemaMerger, SerializerInterface $serializer)
    {
        $this->jsonSchemaMerger = $jsonSchemaMerger;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function guessClass($object, $name, $reference)
    {
        $classes = [];

        foreach ($object->getOneOf() as $key => $oneOf) {
            $oneOfName = $name.'Sub';
            $oneOfResolved = $oneOf;

            if ($oneOf instanceof Reference) {
                $oneOfName = array_pop(explode('/', $oneOf->getFragment()));
                $oneOfResolved = $this->resolve($oneOf, JsonSchema::class);
            }

            $merged = $this->jsonSchemaMerger->merge($object, $oneOfResolved);
            $classes = array_merge($classes, $this->chainGuesser->guessClass($merged, $oneOfName, $reference . '/oneOf/' . $key));
        }

        return $classes;
    }

    /**
     * {@inheritdoc}
     */
    public function guessType($object, $name, $classes)
    {
        $type = new MultipleType($object);

        foreach ($object->getOneOf() as $oneOf) {
            $type->addType($this->chainGuesser->guessType($oneOf, $name, $classes));
        }

        return $type;
    }

    /**
     * {@inheritdoc}
     */
    public function supportObject($object)
    {
        return ($object instanceof JsonSchema) && $object->getType() === 'object' && is_array($object->getOneOf()) && count($object->getOneOf()) > 0;
    }
}
