<?php

namespace Joli\Jane\Guesser;

use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Registry;
use Joli\Jane\Runtime\Reference;
use Joli\Jane\Schema;
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
    public function guessClass($object, $name, $reference, Registry $registry)
    {
        if ($object->isInCurrentDocument()) {
            return [];
        }

        $mergedReference = (string) $object->getMergedUri();

        if ($registry->getSchema($mergedReference) === null) {
            $schema = $registry->getSchema((string)$object->getOriginUri());
            $schema->addReference((string) $object->getMergedUri()->withFragment(''));
        }

        return $this->chainGuesser->guessClass(
            $this->resolve($object, $this->getSchemaClass()),
            $name,
            (string) $object->getMergedUri()->withFragment('') . '#',
            $registry
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param Reference $object
     */
    public function guessType($object, $name, $reference, Registry $registry)
    {
        $resolved = $this->resolve($object, $this->getSchemaClass());
        $classKey = (string) $object->getMergedUri();

        if ((string)$object->getMergedUri() === (string)$object->getMergedUri()->withFragment('')) {
            $classKey .= '#';
        }

        if ($registry->hasClass($classKey)) {
            $name = $registry->getClass($classKey)->getName();
        }

        return $this->chainGuesser->guessType($resolved, $name, $classKey, $registry);
    }

    /**
     * @return string
     */
    protected function getSchemaClass()
    {
        return JsonSchema::class;
    }
}
