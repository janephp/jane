<?php

namespace Joli\Jane\JsonSchema\Guesser;

use Joli\Jane\JsonReference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

trait GuesserResolverTrait
{
    /** @var DenormalizerInterface */
    protected $denormalizer;

    /**
     * Resolve a reference with a denormalizer
     *
     * @param Reference $reference
     * @param string    $class
     *
     * @return mixed
     */
    public function resolve(Reference $reference, $class)
    {
        return $reference->resolve(function ($data) use($reference, $class) {
            return $this->denormalizer->denormalize($data, $class, 'json', [
                'document-origin' => (string) $reference->getMergedUri()->withFragment('')
            ]);
        });
    }
}
