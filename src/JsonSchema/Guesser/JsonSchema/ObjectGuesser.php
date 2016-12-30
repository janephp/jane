<?php

namespace Joli\Jane\JsonSchema\Guesser\JsonSchema;

use Joli\Jane\AstGenerator\Naming;
use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\JsonSchema\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\JsonSchema\Guesser\GuesserInterface;
use Joli\Jane\JsonSchema\Guesser\ModelGuesserInterface;
use Joli\Jane\JsonSchema\Guesser\GuesserResolverTrait;
use Joli\Jane\JsonSchema\Guesser\PropertiesGuesserInterface;
use Joli\Jane\JsonSchema\Guesser\TypeGuesserInterface;
use Joli\Jane\JsonSchema\Model\JsonSchema;
use Joli\Jane\JsonSchema\Registry\Model;
use Joli\Jane\JsonSchema\Registry\Property;
use Joli\Jane\JsonSchema\Registry\Registry;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ObjectGuesser implements GuesserInterface, PropertiesGuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface, ModelGuesserInterface
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
        return ($object instanceof JsonSchema) && $object->getType() === 'object' && $object->getProperties() !== null;
    }

    /**
     * {@inheritdoc}
     *
     * @param JsonSchema $object
     */
    public function registerModel($object, $name, $reference, Registry $registry)
    {
        $schema = $registry->getSchema($reference);
        $schema->addModel(new Model($object, Naming::getClassName($name), $reference));

        foreach ($object->getProperties() as $key => $property) {
            $this->chainGuesser->registerModel($property, $key, $reference . '/properties/' . $key, $registry);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param JsonSchema $object
     */
    public function guessProperties($object, $name, Registry $registry)
    {
        $properties = [];

        foreach ($object->getProperties() as $key => $property) {
            $properties[] = new Property($property, $key, true, true, '', '');
        }

        return $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function guessTypes($object, $name, Registry $registry)
    {
        /*$discriminants = [];
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

        return new ObjectType($object, $this->naming->getClassName($name), $discriminants);
        */
        return [new Type(Type::BUILTIN_TYPE_OBJECT, true, Naming::getClassName($name))];
    }
}
