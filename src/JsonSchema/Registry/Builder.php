<?php

declare(strict_types = 1);

namespace Joli\Jane\JsonSchema\Registry;

use Joli\Jane\JsonSchema\Guesser\ChainGuesser;
use Joli\Jane\JsonSchema\Model\JsonSchema;
use Symfony\Component\Serializer\SerializerInterface;

class Builder
{
    /** @var ChainGuesser */
    private $guesser;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(ChainGuesser $guesser, SerializerInterface $serializer)
    {
        $this->guesser = $guesser;
        $this->serializer = $serializer;
    }

    /**
     * @param Schema[] $schemas
     *
     * @return mixed
     */
    public function build($schemas)
    {
        $registry = new Registry();

        // First pass get all the classes and register schemas
        foreach ($schemas as $schema) {
            $registry->addSchema($schema);
            $jsonSchema = $this->serializer->deserialize(file_get_contents($schema->getName()), JsonSchema::class, 'json', [
                'document-origin' => $schema->getName()
            ]);

            $this->guesser->registerModel($jsonSchema, $schema->getRootName(), $schema->getName(), $registry);
        }

        // Second pass get
        foreach ($registry->getSchemas() as $document => $schema) {
            foreach ($schema->getModels() as $model) {
                $modelObj = $schema->getModel($schema->getNamespace().'\\'.$model);

                /** @var Property $property */
                foreach ($this->guesser->guessProperties(
                    $modelObj->getOrigin(),
                    $modelObj->getName(),
                    $registry
                ) as $property) {
                    foreach ($this->guesser->guessTypes(
                        $property->getOrigin(),
                        $property->getName(),
                        $registry
                    ) as $type) {
                        $property->addType($type);
                    }

                    $modelObj->addProperty($property);
                }
            }
        }

        return $registry;
    }
}
