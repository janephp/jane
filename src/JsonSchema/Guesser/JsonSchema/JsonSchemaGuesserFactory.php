<?php

namespace Joli\Jane\JsonSchema\Guesser\JsonSchema;

use Joli\Jane\JsonSchema\Guesser\ChainGuesser;
use Joli\Jane\JsonSchema\Guesser\ReferenceGuesser;
use Joli\Jane\JsonSchema\JsonSchemaMerger;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class JsonSchemaGuesserFactory
{
    public static function create(DenormalizerInterface $denormalizer)
    {
        $chainGuesser          = new ChainGuesser();
        $merger                = new JsonSchemaMerger();

        $chainGuesser->addGuesser(new ReferenceGuesser($denormalizer));
        $chainGuesser->addGuesser(new DateTimeGuesser());
        $chainGuesser->addGuesser(new SimpleTypeGuesser());
        $chainGuesser->addGuesser(new ArrayGuesser());
        $chainGuesser->addGuesser(new MultipleGuesser());
        $chainGuesser->addGuesser(new ObjectGuesser($denormalizer));
        $chainGuesser->addGuesser(new DefinitionGuesser());
        $chainGuesser->addGuesser(new ItemsGuesser());
        $chainGuesser->addGuesser(new AnyOfGuesser());
        $chainGuesser->addGuesser(new AllOfGuesser($denormalizer));
        $chainGuesser->addGuesser(new OneOfGuesser());
        $chainGuesser->addGuesser(new ObjectOneOfGuesser($merger, $denormalizer));
        $chainGuesser->addGuesser(new PatternPropertiesGuesser());
        $chainGuesser->addGuesser(new AdditionalItemsGuesser());
        $chainGuesser->addGuesser(new AdditionalPropertiesGuesser());

        return $chainGuesser;
    }
}
