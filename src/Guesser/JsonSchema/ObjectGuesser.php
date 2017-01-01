<?php

namespace Joli\Jane\Guesser\JsonSchema;

use Joli\Jane\Generator\Naming;
use Joli\Jane\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\Guesser\Guess\ClassGuess;
use Joli\Jane\Guesser\Guess\ObjectType;
use Joli\Jane\Guesser\Guess\Property;
use Joli\Jane\Guesser\GuesserInterface;
use Joli\Jane\Guesser\ClassGuesserInterface;
use Joli\Jane\Guesser\GuesserResolverTrait;
use Joli\Jane\Guesser\PropertiesGuesserInterface;
use Joli\Jane\Guesser\TypeGuesserInterface;
use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Reference\Resolver;
use Joli\Jane\Registry;
use Joli\Jane\Runtime\Reference;
use Joli\Jane\Schema;
use Symfony\Component\Serializer\SerializerInterface;

class ObjectGuesser implements GuesserInterface, PropertiesGuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface, ClassGuesserInterface
{
    use ChainGuesserAwareTrait;
    use GuesserResolverTrait;

    /**
     * @var \Joli\Jane\Generator\Naming
     */
    protected $naming;

    public function __construct(Naming $naming, SerializerInterface $serializer)
    {
        $this->naming = $naming;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function supportObject($object)
    {
        return ($object instanceof JsonSchema) && $object->getType() === 'object' && $object->getProperties() !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function guessClass($object, $name, $reference, Registry $registry)
    {
        $registry->getSchema($reference)->addClass($reference, new ClassGuess($object, $this->naming->getClassName($name)));

        foreach ($object->getProperties() as $key => $property) {
            $this->chainGuesser->guessClass($property, $key, $reference . '/properties/' . $key, $registry);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function guessProperties($object, $name, Registry $registry)
    {
        $properties = [];

        foreach ($object->getProperties() as $key => $property) {
            $propertyObj = $property;

            if ($propertyObj instanceof Reference) {
                $propertyObj = $this->resolve($propertyObj, JsonSchema::class);
            }

            $type = $propertyObj->getType();
            $nullable = $type == 'null' || (is_array($type) && in_array('null', $type));

            $properties[] = new Property($property, $key, $nullable);
        }

        return $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function guessType($object, $name, Registry $registry, Schema $schema)
    {
        $discriminants = [];
        $required = $object->getRequired() ?: [];

        foreach ($object->getProperties() as $key => $property) {
            if (!in_array($key, $required)) {
                continue;
            }

            if ($property instanceof Reference) {
                $property = $this->resolve($property, JsonSchema::class);
            }

            if ($property->getEnum() !== null) {
                $isSimple = true;
                foreach ($property->getEnum() as $value) {
                    if (is_array($value) || is_object($value)) {
                        $isSimple = false;
                    }
                }
                if ($isSimple) {
                    $discriminants[$key] = $property->getEnum();
                }
            } else {
                $discriminants[$key] = null;
            }
        }

        return new ObjectType($object, $this->naming->getClassName($name), $schema->getNamespace(), $discriminants);
    }
}
