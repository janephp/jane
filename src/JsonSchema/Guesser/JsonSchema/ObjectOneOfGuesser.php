<?php

namespace Joli\Jane\JsonSchema\Guesser\JsonSchema;

use Joli\Jane\JsonReference\Reference;
use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\JsonSchema\Guesser\GuesserInterface;
use Joli\Jane\JsonSchema\Guesser\GuesserResolverTrait;
use Joli\Jane\JsonSchema\Guesser\ModelGuesserInterface;
use Joli\Jane\JsonSchema\Guesser\TypeGuesserInterface;
use Joli\Jane\JsonSchema\JsonSchemaMerger;
use Joli\Jane\JsonSchema\Model\JsonSchema;
use Joli\Jane\JsonSchema\Registry\Registry;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ObjectOneOfGuesser implements GuesserInterface, TypeGuesserInterface, ModelGuesserInterface, ChainGuesserAwareInterface
{
    use ChainGuesserAwareTrait;
    use GuesserResolverTrait;

    /**
     * @var \Joli\Jane\JsonSchema\JsonSchemaMerger
     */
    private $jsonSchemaMerger;

    public function __construct(JsonSchemaMerger $jsonSchemaMerger, DenormalizerInterface $denormalizer)
    {
        $this->jsonSchemaMerger = $jsonSchemaMerger;
        $this->denormalizer = $denormalizer;
    }

    /**
     * {@inheritdoc}
     *
     * @param JsonSchema $object
     */
    public function registerModel($object, $name, $reference, Registry $registry)
    {
        foreach ($object->getOneOf() as $key => $oneOf) {
            $oneOfName = $name.'Sub';
            $oneOfResolved = $oneOf;

            if ($oneOf instanceof Reference) {
                $oneOfName = array_pop(explode('/', $oneOf->getMergedUri()->getFragment()));
                $oneOfResolved = $this->resolve($oneOf, JsonSchema::class);
            }

            $merged = $this->jsonSchemaMerger->merge($object, $oneOfResolved);

            $this->chainGuesser->registerModel($merged, $oneOfName, $reference . '/oneOf/' . $key, $registry);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param JsonSchema $object
     */
    public function guessTypes($object, $name, Registry $registry)
    {
        $types = [];

        foreach ($object->getOneOf() as $oneOf) {
            $types = array_merge($types, $this->chainGuesser->guessTypes($oneOf, $name, $registry));
        }

        return $types;
    }

    /**
     * {@inheritdoc}
     */
    public function supportObject($object)
    {
        return ($object instanceof JsonSchema) && $object->getType() === 'object' && is_array($object->getOneOf()) && count($object->getOneOf()) > 0;
    }
}
