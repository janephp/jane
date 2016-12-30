<?php

namespace Joli\Jane\JsonSchema\Guesser;

use Joli\Jane\JsonReference\Reference;
use Joli\Jane\JsonSchema\Registry\Registry;
use Joli\Jane\JsonSchema\Model\JsonSchema;
use Joli\Jane\JsonSchema\Registry\Schema;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ReferenceGuesser implements ModelGuesserInterface, GuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface
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
    public function supportObject($object)
    {
        return $object instanceof Reference;
    }

    /**
     * {@inheritdoc}
     *
     * @param Reference $object
     */
    public function registerModel($object, $name, $reference, Registry $registry)
    {
        if ($object->isInCurrentDocument()) {
            return [];
        }

        $modelSchemaName = (string) $object->getMergedUri()->withFragment('');

        if ($registry->getSchema($modelSchemaName) === null) {
            $originSchema = $registry->getSchema((string)$object->getOriginUri()->withFragment(''));

            $registry->addSchema(new Schema($modelSchemaName, $originSchema->getNamespace(), $originSchema->getDirectory(), $name));
        }

        return $this->chainGuesser->registerModel(
            $this->resolve($object, JsonSchema::class),
            $name,
            (string) $object->getMergedUri(),
            $registry
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param Reference $object
     */
    public function guessTypes($object, $name, Registry $registry)
    {
        $resolved = $this->resolve($object, JsonSchema::class);
        $schema = $registry->getSchema((string) $object->getMergedUri()->withFragment(''));

        if ($schema === null) {
            $schema = $registry->getSchema((string) $object->getOriginUri()->withFragment(''));
        }

        $classKey = (string) $object->getMergedUri();

        if ($schema->getModelByReference($classKey) !== null) {
            $name = $schema->getModelByReference($classKey)->getName();
        }

        return $this->chainGuesser->guessTypes($resolved, $name, $registry);
    }
}
